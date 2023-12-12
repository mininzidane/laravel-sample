<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Requests\Queries\HippoQuery;
use App\Models\Authorization\Subdomain;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

abstract class ApiHippoQuery extends HippoQuery
{
	/**
	 * Override this in your queries or mutations
	 * to provide custom authorization.
	 *
	 * @param mixed $root
	 * @param array $args
	 * @param mixed $ctx
	 * @param ResolveInfo|null $resolveInfo
	 * @param Closure|null $getSelectFields
	 * @return bool
	 */
	public function authorize(
		$root,
		array $args,
		$ctx,
		ResolveInfo $resolveInfo = null,
		Closure $getSelectFields = null
	): bool {
		if (!array_key_exists("subdomain", $args)) {
			throw new MissingMandatoryParametersException(
				"A subdomain must be specified on the base graphql request",
			);
		}

		$user = $this->guard()->user();

		if (!$user->hasPermissionTo("GraphQL: Access Api", "api")) {
			return false;
		}

		if (
			$this->permissionName != null &&
			!$user->hasPermissionTo($this->permissionName, "api")
		) {
			return false;
		}

		if ($user->hasPermissionTo("GraphQL: Access All Subdomains", "api")) {
			return true;
		}

		$subdomain = Subdomain::with("permission")
			->where("name", "=", $args["subdomain"])
			->first();

		if (!$subdomain) {
			return false;
		}

		if (!$user->hasPermissionTo($subdomain->permission->id, "api")) {
			return false;
		}

		return true;
	}

	/**
	 * @param Request|null $request
	 * @return StatefulGuard
	 */
	protected function guard(Request $request = null)
	{
		return Auth::guard();
	}
}
