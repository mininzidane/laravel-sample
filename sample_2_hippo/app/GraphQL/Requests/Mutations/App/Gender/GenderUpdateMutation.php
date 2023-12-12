<?php

namespace App\GraphQL\Requests\Mutations\App\Gender;

use Closure;
use Exception;
use App\Models\Gender;
use Illuminate\Validation\Rule;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\HippoGraphQLActionCodes;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class GenderUpdateMutation extends AppHippoMutation
{
	protected $model = Gender::class;

	protected $permissionName = "Genders: Update";

	protected $attributes = [
		"name" => "GenderUpdate",
		"model" => Gender::class,
	];

	protected $actionId = HippoGraphQLActionCodes::GENDER_UPDATE;

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
			"input.sex.in" => "Sex must be M or F",
			"input.species.required" => "A species must be selected",
		];
	}

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
			],
			"input" => [
				"type" => GraphQL::type("GenderUpdateInput"),
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
				)
					->where(function ($query) use ($args) {
						return $query
							->where("gender", $args["input"]["name"])
							->where("species", $args["input"]["species"])
							->where("deleted_at", null);
					})
					->ignore($args["id"]),
			],
			"input.sex" => [Rule::in(["F", "M"])],
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
			->where("gender", $args["input"]["name"])
			->where("species", $args["input"]["species"])
			->onlyTrashed()
			->first();

		// It exists so modify deleted so new name can be used (append its id)
		if ($existing) {
			$existing->update([
				"gender" => $existing->gender . "_" . $existing->id,
			]);
		}

		// Fixing column name issue - better solution to come
		$args["input"]["gender"] = $args["input"]["name"];
		unset($args["input"]["name"]);

		$modelInstance->update($args["input"]);
		$this->affectedId = $args["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
