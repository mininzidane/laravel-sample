<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\HippoGraphQLType;
use Closure;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Field;

use function array_merge;

abstract class HippoField extends Field
{
	protected $user = null;
	protected $authorized = false;
	protected $isList = true;
	protected $args = [];
	protected $graphQLType = HippoGraphQLType::class;

	protected $permissionName = null;

	protected $appPermissions = [];
	protected $apiPermissions = [];

	protected $guardName = "api";
	protected $guard = null;

	protected $attributes = [
		"description" => "Default field description",
	];

	/**
	 * HippoField constructor.
	 *
	 * Valid settings are:
	 *  - description: string
	 *  - isList: bool
	 *
	 * @param array $settings
	 */
	public function __construct(array $settings = [])
	{
		if (
			array_key_exists("isList", $settings) &&
			is_bool($settings["isList"])
		) {
			$this->isList = $settings["isList"];
		}

		if (request()->header("Subdomain")) {
			$this->guardName = "api-subdomain-passport";
		}

		if (array_key_exists("args", $settings)) {
			$this->args = $settings["args"];
		}

		$this->attributes = array_merge($this->attributes, $settings);
	}

	public function authorize(
		$root,
		array $args,
		$ctx,
		ResolveInfo $resolveInfo = null,
		Closure $getSelectFields = null
	): bool {
		$this->user = $this->guard($args)->user();

		if ($this->requestIsFromSubdomain($args)) {
			return $this->authorizeSubdomainUser();
		}

		return $this->authorizeAPIUser();
	}

	protected function requestIsFromSubdomain($args)
	{
		// The header is only set on requests authenticating with a subdomain token
		$subdomainHeaderSet = request()->header("Subdomain");

		// The subdomain selection should only exist on requests made via the api endpoints
		$subdomainSelectionSet = array_key_exists("subdomain", $args);

		if (!$subdomainSelectionSet && $subdomainHeaderSet) {
			return true;
		}

		return false;
	}

	protected function authorizeSubdomainUser()
	{
		if (empty($this->appPermissions)) {
			$this->authorized = true;
			return true;
		}

		foreach ($this->appPermissions as $appPermission) {
			if (
				!$this->user->hasPermissionTo(
					$appPermission,
					"api-subdomain-passport",
				)
			) {
				$this->authorized = false;
				return false;
			}
		}

		$this->authorized = true;
		return true;
	}

	protected function authorizeAPIUser()
	{
		if (empty($this->apiPermissions)) {
			$this->authorized = true;
			return true;
		}

		foreach ($this->apiPermissions as $apiPermission) {
			if (!$this->user->hasPermissionTo($apiPermission, "api")) {
				$this->authorized = false;
				return false;
			}
		}

		$this->authorized = true;
		return true;
	}

	public function type(): Type
	{
		$graphQLType = call_user_func([
			$this->graphQLType,
			"getGraphQLTypeName",
		]);

		if ($this->isList) {
			return Type::listOf(GraphQL::type($graphQLType));
		}

		return GraphQL::type($graphQLType);
	}

	public function args(): array
	{
		return $this->args;
	}

	public function resolve($source, $args, $context, ResolveInfo $info)
	{
		if (!$this->authorized) {
			return null;
		}

		$defaultResolver = Executor::getDefaultFieldResolver();

		return $defaultResolver($source, $args, $context, $info);
	}

	protected function getProperty(): string
	{
		return $this->attributes["alias"] ?? $this->attributes["name"];
	}

	/**
	 * Get the guard to be used during authentication.
	 *
	 * @param Array $args
	 * @return StatefulGuard
	 */
	protected function guard($args)
	{
		if ($this->requestIsFromSubdomain($args)) {
			return Auth::guard("api-subdomain-passport");
		}

		return Auth::guard();
	}
}
