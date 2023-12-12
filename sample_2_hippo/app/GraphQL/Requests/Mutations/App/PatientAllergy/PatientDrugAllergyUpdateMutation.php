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

class PatientDrugAllergyUpdateMutation extends AppHippoMutation
{
	protected $model = PatientDrugAllergy::class;

	protected $permissionName = "Patient Drug Allergies: Update";

	protected $attributes = [
		"name" => "PatientDrugAllergyUpdate",
		"model" => PatientDrugAllergy::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_DRUG_ALLERGY_UPDATE;

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
				"type" => GraphQL::type("PatientDrugAllergyInput"),
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

		if (!empty($args["input"]["allergy"])) {
			$allergies = explode(",", $args["input"]["allergy"]);
			foreach ($allergies as $key => $input) {
				$modelInstance->withTrashed()->updateOrCreate(
					[
						"client_id" => $args["input"]["client_id"],
						"allergy" => $input,
					],
					["removed" => 0, "deleted_at" => null],
				);
			}

			$modelInstance
				->where("client_id", "=", $args["input"]["client_id"])
				->whereNotIn("allergy", $allergies)
				->update(["removed" => "1"]);

			$modelInstance
				->where("client_id", "=", $args["input"]["client_id"])
				->whereNotIn("allergy", $allergies)
				->delete();
		}

		$this->affectedId = $args["input"]["client_id"];

		return $this->model
			::on($this->subdomainName)
			->where("client_id", $args["input"]["client_id"])
			->paginate();
	}
}
