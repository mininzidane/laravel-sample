<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientAllergy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;

class PatientAllergyDeleteMutation extends AppHippoMutation
{
	protected $model = PatientAllergy::class;

	protected $permissionName = "Patient Allergies: Delete";

	protected $attributes = [
		"name" => "PatientAllergyDelete",
		"model" => PatientAllergy::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_ALLERGY_DELETE;

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
		$modelInstance->delete();

		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
