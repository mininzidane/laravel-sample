<?php

namespace App\GraphQL\Requests;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Arguments\DatedArguments;
use App\GraphQL\Arguments\DefaultArguments;
use App\GraphQL\Arguments\EmailArguments;
use App\GraphQL\Arguments\NameArguments;
use App\GraphQL\Arguments\PhoneArguments;
use App\GraphQL\Arguments\SoftDeleteArguments;
use App\GraphQL\Arguments\TimestampArguments;
use App\GraphQL\Resolvers\DefaultResolver;
use App\Models\Authorization\Subdomain;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

abstract class HippoRequest extends Query
{
	protected $model = null;
	protected $args = null;
	protected $argService = null;
	protected $graphQLType = null;
	protected $permissionName = null;

	protected $arguments = [];

	public function __construct()
	{
		$model = new $this->model();

		if ($model->timestamps) {
			$this->arguments[] = TimestampArguments::class;
		}

		if ($model->primaryDateField) {
			$this->arguments[] = DatedArguments::class;
		}

		if ($model->soft_deleting) {
			$this->arguments[] = SoftDeleteArguments::class;
		}

		if ($model->hasEmailAddress) {
			$this->arguments[] = EmailArguments::class;
		}

		if ($model->hasPhoneNumber) {
			$this->arguments[] = PhoneArguments::class;
		}

		if ($model->hasName) {
			$this->arguments[] = NameArguments::class;
		}

		$argService = new DefaultArguments($model);

		if ($this->arguments) {
			foreach ($this->arguments as $argument) {
				$argService = new $argument($argService);
			}
		}

		$this->argService = $argService;
	}

	public function respondWithUnauthorized()
	{
		response()->isForbidden();
	}

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

		if (!$user || !$user->hasPermissionTo("GraphQL: Access Api", "api")) {
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

	public function args(): array
	{
		return $this->argService->getArgs();
	}

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if (
			!request()->hasHeader("Subdomain") &&
			request()->header("Subdomain")
		) {
			$args["subdomain"] = request()->header("subdomain");
		}

		$resolver = new DefaultResolver(
			$this->model,
			$root,
			$args,
			$context,
			$resolveInfo,
		);
		$model = new $this->model();

		foreach ($this->arguments as $argument) {
			$argumentResolver = $argument::getResolver();

			$resolver = new $argumentResolver($resolver, $model);
		}

		$query = $resolver->getQuery($getSelectFields);

		$limit = array_key_exists("limit", $args) ? $args["limit"] : 10;
		$page = array_key_exists("page", $args) ? $args["page"] : 1;

		return $query->paginate($limit, ["*"], "page", $page);
	}

	public function connectToSubdomain($subdomain)
	{
		$subdomainName = $subdomain;

		$connectionDetails = Config::get("database.connections.hippodb");
		$connectionDetails["database"] = "hippodb_" . $subdomainName;
		$connectionName = "database.connections." . $subdomainName;

		Config::set($connectionName, $connectionDetails);

		try {
			DB::connection($subdomainName)->getPdo();
			return $subdomainName . ".";
		} catch (Exception $e) {
			error_log($e);
			throw new SubdomainNotConfiguredException($subdomainName);
		}
	}

	public function type(): Type
	{
		return GraphQL::paginate(
			$this->model::getGraphQLType()::getGraphQLTypeName(),
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
		return Auth::guard();
	}
}
