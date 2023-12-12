<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Requests\Queries\HippoQuery;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

abstract class AppHippoQuery extends HippoQuery
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
		if (!request()->header("Subdomain")) {
			throw new MissingMandatoryParametersException(
				"A subdomain must be provided for this request",
			);
		}

		$user = $this->guard()->user();

		if (!$user) {
			return false;
		}

		return $this->checkPermissions($user, $args);
	}

	protected function checkPermissions(
		Authenticatable $user,
		array $args = null
	) {
		return true;
	}

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if (request()->header("Subdomain")) {
			$args["subdomain"] = request()->header("subdomain");
		}

		return parent::resolve(
			$root,
			$args,
			$context,
			$resolveInfo,
			$getSelectFields,
		);
	}

	/**
	 * Get the guard to be used during authentication.
	 *
	 * @param Request $request
	 * @return StatefulGuard
	 */
	protected function guard(Request $request = null)
	{
		return Auth::guard("api-subdomain-passport");
	}
}
