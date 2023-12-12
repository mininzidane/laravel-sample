<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientDrugAllergy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PatientDrugAllergyCreateMutation extends AppHippoMutation
{
	protected $model = PatientDrugAllergy::class;

	protected $permissionName = "Patient Drug Allergies: Create";

	protected $attributes = [
		"name" => "PatientDrugAllergyCreate",
		"model" => PatientDrugAllergy::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_DRUG_ALLERGY_CREATE;

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
			"input" => ["type" => GraphQL::type("PatientDrugAllergyInput")],
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
	 * @return mixed |null * @throws SubdomainNotConfiguredException
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

		$this->affectedId = $modelInstance->create($args["input"])->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate();
	}
}
