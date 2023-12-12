<?php

namespace App\GraphQL\Requests\Mutations\App\PatientAllergy;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\PatientAllergyNote;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PatientAllergyNoteUpdateMutation extends AppHippoMutation
{
	protected $model = PatientAllergyNote::class;

	protected $permissionName = "Patient Allergy Notes: Update";

	protected $attributes = [
		"name" => "PatientAllergyNoteUpdate",
		"model" => PatientAllergyNote::class,
	];

	protected $actionId = HippoGraphQLActionCodes::PATIENT_ALLERGY_NOTE_UPDATE;

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
				"type" => GraphQL::type("PatientAllergyNoteInput"),
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
		$this->prepareResolve($args);

		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		if ($args["input"]["note"] == "") {
			$modelInstance->findOrFail($args["input"]["client_id"])->delete();
		} else {
			$modelInstance->updateOrCreate(
				["client_id" => $args["input"]["client_id"]],
				$args["input"],
			);
		}

		$this->affectedId = $args["input"]["client_id"];

		return $this->model
			::on($this->subdomainName)
			->where("client_id", $args["input"]["client_id"])
			->paginate(1);
	}
}
