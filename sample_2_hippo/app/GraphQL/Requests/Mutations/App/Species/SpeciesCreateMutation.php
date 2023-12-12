<?php

namespace App\GraphQL\Requests\Mutations\App\Species;

use Closure;
use App\Models\Species;
use Illuminate\Validation\Rule;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class SpeciesCreateMutation extends AppHippoMutation
{
	protected $model = Species::class;

	protected $permissionName = "Species: Create";

	protected $attributes = [
		"name" => "SpeciesCreate",
		"model" => Species::class,
	];

	protected $actionId = HippoGraphQLActionCodes::SPECIES_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.unique" => "The name must be unique",
			"input.name.required" => "The name must not be blank",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("SpeciesCreateInput"),
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
					request()->header("Subdomain") . ".tblSpecies",
					"name",
				)->where(function ($query) use ($args) {
					return $query
						->where("name", $args["input"]["name"])
						->where("deleted_at", null);
				}),
			],
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

		$species = $modelInstance
			->withTrashed()
			->updateOrCreate(
				["name" => $args["input"]["name"]],
				["name" => $args["input"]["name"]],
			);

		if ($species->trashed()) {
			$species->restore();
		}

		$this->affectedId = $species->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
