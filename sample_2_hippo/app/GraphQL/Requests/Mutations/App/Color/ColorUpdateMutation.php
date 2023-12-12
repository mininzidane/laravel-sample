<?php

namespace App\GraphQL\Requests\Mutations\App\Color;

use Closure;
use Exception;
use App\Models\Color;
use Illuminate\Validation\Rule;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\HippoGraphQLActionCodes;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class ColorUpdateMutation extends AppHippoMutation
{
	protected $model = Color::class;

	protected $permissionName = "Colors: Update";

	protected $attributes = [
		"name" => "ColorUpdate",
		"model" => Color::class,
	];

	protected $actionId = HippoGraphQLActionCodes::COLOR_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "The color must not be blank",
			"input.name.unique" =>
				"The color must be unique for the selected species",
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
				"type" => GraphQL::type("ColorUpdateInput"),
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
					request()->header("Subdomain") . ".tblColors",
					"name",
				)->where(function ($query) use ($args) {
					return $query
						->where("name", $args["input"]["name"])
						->where("species", $args["input"]["species"])
						->where("deleted_at", null);
				}),
			],
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
