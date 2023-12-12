<?php

namespace App\GraphQL\Requests\Mutations\App\User;

use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\User;
use App\Models\UserLocation;
use Closure;
use Exception;
use GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class UserUpdateMutation extends AppHippoMutation
{
	protected $model = User::class;

	protected $permissionName = "Users: Update";

	protected $attributes = [
		"name" => "UserUpdate",
		"model" => User::class,
	];

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
				"default" => null,
				"rules" => ["required"],
			],
			"input" => [
				"type" => GraphQL::type("UserUpdateInput"),
			],
		];
	}

	public function validationErrorMessages(array $args = []): array
	{
		return [
			"input.username.required" => "Username is required",
			"input.username.max" =>
				"Username cannot be longer than 191 characters",
			"input.username.email" => "Username must be a valid email address.",
			"input.username.unique" =>
				"An account exists already with this username",
			"input.lastName.required" => "Last name is required",
			"input.lastName.max" =>
				"Last name cannot be longer than 50 characters",
			"input.degree.exists" => "The specified degree does not exists",
			"input.lastLocationId.exists" =>
				"The specified last location does not exist.",
			"input.locations.exists" => "A specified location does not exist.",
			"input.roles.required" => "A user role is required",
			"input.roles.exists" => "A specified role does not exist",
		];
	}

	public function checkPermissions(Authenticatable $user, array $args = null)
	{
		if (!$args["id"]) {
			$args["id"] = $this->guard()->user()->id;
		}

		return $user->hasPermissionTo(
			$user->id === $args["id"]
				? "Users: Update Self"
				: $this->permissionName,
		);
	}

	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectedFields
	) {
		/** @var User $user */
		$user = $this->model::on($this->subdomainName)->findOrFail($args["id"]);
		$user->fill($args["input"]);

		if (isset($args["input"]["locations"])) {
			$user->locations()->sync($args["input"]["locations"]);
		}

		$user->save();

		//$user->syncRole($args["input"]["roles"]);
		/**
			TODO: The below section to connect roles to access levels
			and insert into the database should be replaced by the
			above line once access levels are no longer needed
			for Zend.
		**/

		if (isset($args["input"]["roles"])) {
			$accessLevels = DB::connection($this->subdomainName)
				->table("tblAccessLevels")
				->get();

			$userAccessLevels = collect($args["input"]["roles"])
				->map(function ($role) use ($user, $accessLevels) {
					return [
						"user_id" => $user->id,
						"access_level" => $accessLevels
							->where("role_id", $role)
							->first()->al,
					];
				})
				->toArray();

			DB::connection($this->subdomainName)
				->table("tblUserAccessLevels")
				->where("user_id", $user->id)
				->delete();

			DB::connection($this->subdomainName)
				->table("tblUserAccessLevels")
				->insert($userAccessLevels);
		}

		return User::on($this->subdomainName)
			->where("id", $user->id)
			->paginate(1);
	}
}
