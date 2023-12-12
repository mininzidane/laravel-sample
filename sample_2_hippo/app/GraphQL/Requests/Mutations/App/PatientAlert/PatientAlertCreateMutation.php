<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAlert;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientAlert;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PatientAlertCreateMutation extends AppHippoMutation
{
	protected $model = PatientAlert::class;

	protected $permissionName = "Patient Alerts: Create";

	protected $attributes = [
		"name" => "PatientAlertCreate",
		"model" => PatientAlert::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_ALERT_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("PatientAlertInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return mixed |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$id = $modelInstance->create($args["input"])->id;

		$this->affectedId = $id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $id)
			->paginate(1);
	}
}
