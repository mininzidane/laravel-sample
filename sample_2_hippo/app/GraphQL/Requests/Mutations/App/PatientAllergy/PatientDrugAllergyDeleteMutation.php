<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientDrugAllergy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;

class PatientDrugAllergyDeleteMutation extends AppHippoMutation
{
	protected $model = PatientDrugAllergy::class;

	protected $permissionName = "Patient Drug Allergies: Delete";

	protected $attributes = [
		"name" => "PatientDrugAllergyDelete",
		"model" => PatientDrugAllergy::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_DRUG_ALLERGY_DELETE;

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
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$modelInstance = $this->model
			::on($this->subdomainName)
			->findOrFail($args["id"]);

		$modelInstance->update(["removed" => "1"]);
		$modelInstance->delete();

		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
