<?php

namespace App\GraphQL\Requests\Mutations\App\User;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\User;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UserDeleteMutation extends AppHippoMutation
{
	protected $model = User::class;
	protected $permissionName = "Users: Delete";

	protected $attributes = [
		"name" => "UserDelete",
		"model" => User::class,
	];

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
				"rules" => ["required"],
			],
		];
	}

	public function validationErrorMessages(array $args = []): array
	{
		return [
			"id.required" => "An ID is required.",
		];
	}

	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		/** @var User $user */
		$user = User::on($this->subdomainName)->findOrFail($args["id"]);
		// this fires a trigger that updates deleted_at
		$user->update(["removed" => 1]);

		$this->affectedId = $user->id;

		return User::on($this->subdomainName)
			->where("id", $user->id)
			->paginate(1);
	}
}
