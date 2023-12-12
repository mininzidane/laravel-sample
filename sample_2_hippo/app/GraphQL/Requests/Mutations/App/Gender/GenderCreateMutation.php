<?php

namespace App\GraphQL\Requests\Mutations\App\Gender;

use Closure;
use App\Models\Gender;
use Illuminate\Validation\Rule;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class GenderCreateMutation extends AppHippoMutation
{
	protected $model = Gender::class;

	protected $permissionName = "Genders: Create";

	protected $attributes = [
		"name" => "GenderCreate",
		"model" => Gender::class,
	];

	protected $actionId = HippoGraphQLActionCodes::GENDER_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.unique" =>
				"The name must be unique for a selected species",
			"input.name.required" => "The gender name must not be blank",
			"input.sex.required" => "Please select an option for sex",
			"input.sex.in" => "Sex must be M or F",
			"input.species.required" => "A species must be selected",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("GenderCreateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => [
				"required",
				"max:191",
				Rule::unique(
					request()->header("Subdomain") . ".tblGenders",
					"gender",
				)->where(function ($query) use ($args) {
					return $query
						->where("gender", $args["input"]["name"])
						->where("species", $args["input"]["species"])
						->where("deleted_at", null);
				}),
			],
			"input.sex" => ["required", Rule::in(["F", "M"])],
			"input.species" => ["required"],
		];
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

		$gender = $modelInstance->withTrashed()->updateOrCreate(
			[
				"gender" => $args["input"]["name"],
				"species" => $args["input"]["species"],
			],
			[
				"gender" => $args["input"]["name"],
				"sex" => $args["input"]["sex"],
				"neutered" => $args["input"]["neutered"],
				"species" => $args["input"]["species"],
			],
		);

		if ($gender->trashed()) {
			$gender->restore();
		}

		$this->affectedId = $gender->id;
		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
