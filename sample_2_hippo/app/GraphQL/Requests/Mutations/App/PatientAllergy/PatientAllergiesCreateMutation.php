<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientAllergy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;

class PatientAllergiesCreateMutation extends AppHippoMutation
{
	protected $model = PatientAllergy::class;

	protected $permissionName = "Patient Allergies: Create";

	protected $attributes = [
		"name" => "PatientAllergiesCreate",
		"model" => PatientAllergy::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_ALLERGY_CREATE;

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
				"type" => Type::listOf(
					GraphQL::type("PatientAllergyCreateInput"),
				),
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

		$createdIDs = [];

		foreach ($args["input"] as $newAllergy) {
			$modelInstance = new $this->model();
			$modelInstance->setConnection($this->subdomainName);

			$this->affectedId ??= $newAllergy["client_id"];

			$allergy = $modelInstance
				->withTrashed()
				->updateOrCreate($newAllergy, $newAllergy);

			if ($allergy->trashed()) {
				$allergy->restore();
			}

			$createdIDs[] = $allergy->id;
		}

		return $this->model
			::on($this->subdomainName)
			->whereIn($modelInstance->getPrimaryKey(), $createdIDs)
			->paginate();
	}
}
