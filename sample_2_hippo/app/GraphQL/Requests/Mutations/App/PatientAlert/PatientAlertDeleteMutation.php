<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAlert;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientAlert;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;

class PatientAlertDeleteMutation extends AppHippoMutation
{
	protected $model = PatientAlert::class;

	protected $permissionName = "Patient Alerts: Delete";

	protected $attributes = [
		"name" => "PatientAlertDelete",
		"model" => PatientAlert::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_ALERT_DELETE;

	public function __construct()
	{
		return parent::__construct();
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
		$modelInstance = $this->model
			::on($this->subdomainName)
			->findOrFail($args["id"]);
		$modelInstance->update(["removed" => "1"]);
		$modelInstance->setConnection($this->subdomainName);

		$modelInstance->delete();

		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
