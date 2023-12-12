<?php

namespace App\GraphQL\Requests\Mutations\App;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\HippoMutation;
use App\Models\Log;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

abstract class AppHippoMutation extends HippoMutation
{
	protected $model = null;
	protected $args = null;
	protected $argService = null;
	protected $graphQLType = null;
	protected $permissionName = null;
	protected $subdomainName;

	protected $actionId = null;
	protected $affectedId = null;

	protected $arguments = [];

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
		if (!request()->hasHeader("Subdomain")) {
			throw new MissingMandatoryParametersException(
				"A subdomain token must be provided for this request",
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
		return $user->hasPermissionTo($this->permissionName);
	}

	public function args(): array
	{
		$fields = [
			"id" => [
				"name" => "id",
				"type" => Type::string(),
			],
			"subdomain" => [
				"name" => "subdomain",
				"type" => Type::string(),
			],
		];

		$additionalFields = $this->getFields();

		foreach ($additionalFields as $additionalFieldKey => $additionalField) {
			$type = Type::string();

			$fields[$additionalFieldKey] = [
				"name" => $additionalFieldKey,
				"type" => $type,
			];
		}

		return $fields;
	}

	public function getFields()
	{
		$modelGraphqlType = $this->model::getGraphQLType();

		$columns = (new $modelGraphqlType())->columns();

		$guardedFields = (new $this->model())->getGuarded();

		return array_filter(
			$columns,
			function ($column) use ($guardedFields) {
				return !in_array($column, $guardedFields);
			},
			ARRAY_FILTER_USE_KEY,
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

	protected function prepareResolve($args)
	{
		if (request()->hasHeader("Subdomain")) {
			$args["subdomain"] = request()->header("subdomain");
		}

		$this->subdomainName = $args["subdomain"];
		$this->args = $args;

		$this->connectToSubdomain($this->subdomainName);
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return |null
	 * @throws \Exception
	 */
	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$this->prepareResolve($args);

		try {
			$result = DB::connection($this->subdomainName)->transaction(
				function () use (
					$root,
					$args,
					$context,
					$resolveInfo,
					$getSelectFields
				) {
					return $this->resolveTransaction(
						$root,
						$args,
						$context,
						$resolveInfo,
						$getSelectFields,
					);
				},
			);

			$this->logTransaction(true);

			return $result;
		} catch (\Exception $e) {
			$this->logTransaction(false);

			throw $e;
		}
	}

	protected function logTransaction($success)
	{
		if (!$this->actionId) {
			return;
		}

		$user = $this->guard()->user();
		$this->args["input"]["success"] = $success;

		$affectedIds = is_array($this->affectedId)
			? $this->affectedId
			: [$this->affectedId];
		foreach ($affectedIds as $affectedId) {
			Log::on($this->subdomainName)->create([
				"organization_id" => $user->organization
					? $user->organization->id
					: null,
				"location_id" => $user->lastLocation
					? $user->lastLocation->id
					: null,
				"user_id" => $user->id,
				"action_id" => $this->actionId,
				"affected_id" => $affectedId ?: null,
				"information" => json_encode($this->args["input"]),
				"screen" => "/",
			]);
		}
	}

	protected function fetchInput($inputName, $defaultValue = false)
	{
		return array_key_exists($inputName, $this->args["input"])
			? $this->args["input"][$inputName]
			: $defaultValue;
	}

	protected function fetchDateInput($inputName)
	{
		return array_key_exists($inputName, $this->args["input"])
			? (new Carbon($this->args["input"][$inputName]))->format("Y-m-d")
			: Carbon::now()->format("Y-m-d");
	}
}
