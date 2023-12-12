<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\UserArguments;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;

class UserQuery extends AppHippoQuery
{
	protected $model = User::class;

	protected $permissionName = "Users: Read";

	protected $attributes = [
		"name" => "userQuery",
	];

	protected $arguments = [UserArguments::class];

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if (
			!array_key_exists("email", $args) &&
			!array_key_exists("isProvider", $args) &&
			!array_key_exists("name", $args) &&
			!array_key_exists("username", $args)
		) {
			$args = $this->setUserIdIfNoneProvided($args);
		}

		return parent::resolve(
			$root,
			$args,
			$context,
			$resolveInfo,
			$getSelectFields,
		);
	}

	protected function checkPermissions(
		Authenticatable $user,
		array $args = null
	) {
		$args = $this->setUserIdIfNoneProvided($args);

		if (
			$args["id"] != $user->id &&
			!$user->hasPermissionTo($this->permissionName)
		) {
			return false;
		}

		return true;
	}

	public function setUserIdIfNoneProvided($args)
	{
		if (!array_key_exists("id", $args)) {
			$args["id"] = $this->guard()->user()->id;
		}

		return $args;
	}
}
