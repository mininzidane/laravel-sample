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

class UserCreateMutation extends AppHippoMutation
{
	protected $model = User::class;

	protected $permissionName = "Users: Create";

	protected $attributes = [
		"name" => "UserCreate",
		"model" => User::class,
	];

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("UserCreateInput"),
			],
		];
	}

	public function validationErrorMessages(array $args = []): array
	{
		return [
			"input.username.required" => "Username is required",
			"input.username.max" =>
				"Username cannot be longer than 191 characters",
			"input.username.unique" =>
				"An account exists already with this username. Contact support if you need to restore a previously deleted user.",
			"input.lastName.required" => "Last name is required",
			"input.lastName.max" =>
				"Last name cannot be longer than 50 characters",
			"input.degree.exists" => "The specified degree does not exists",
			"input.locations.required" => "A location is required",
			"input.locations.exists" => "A specified location does not exist.",
			"input.roles.required" => "A user role is required",
			"input.roles.exists" => "A specified role does not exist",
		];
	}

	public function checkPermissions(Authenticatable $user, array $args = null)
	{
		return $user->hasPermissionTo($this->permissionName);
	}

	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectedFields
	) {
		/** @var User $user */
		$user = User::on($this->subdomainName)->create($args["input"]);
		$user->locations()->attach($args["input"]["locations"]);
		$user->save();

		//$user->assignRole($args["input"]["roles"]);
		/**
			TODO: The below section to connect roles to access levels
			and insert into the database should be replaced by the
			above line once access levels are no longer needed
			for Zend.
		**/

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
			->insert($userAccessLevels);

		return User::on($this->subdomainName)
			->where("id", $user->id)
			->paginate(1);
	}
}
