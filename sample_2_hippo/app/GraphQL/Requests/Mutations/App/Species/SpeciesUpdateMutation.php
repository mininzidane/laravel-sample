<?php

namespace App\GraphQL\Requests\Mutations\App\Species;

use Closure;
use Exception;
use App\Models\Species;
use Illuminate\Validation\Rule;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\HippoGraphQLActionCodes;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class SpeciesUpdateMutation extends AppHippoMutation
{
	protected $model = Species::class;

	protected $permissionName = "Species: Update";

	protected $attributes = [
		"name" => "SpeciesUpdate",
		"model" => Species::class,
	];

	protected $actionId = HippoGraphQLActionCodes::SPECIES_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "The name must not be blank",
			"input.name.unique" => "The name must be unique",
		];
	}

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
			],
			"input" => [
				"type" => GraphQL::type("SpeciesUpdateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => [
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
		$modelInstance = $this->model
			::on($this->subdomainName)
			->find($args["id"]);

		if (!$modelInstance) {
			throw new Exception(
				"Cannot contain non-existent item: " . $args["id"],
			);
		}

		// Does the updated name exists as a deleted record?
		$existing = $this->model
			::on($this->subdomainName)
			->where("name", $args["input"]["name"])
			->onlyTrashed()
			->first();

		// It exists so modify deleted so new name can be used (append its id)
		if ($existing) {
			$existing->update([
				"name" => $existing->name . "_" . $existing->id,
			]);
		}
		$modelInstance->update($args["input"]);

		$this->affectedId = $args["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
