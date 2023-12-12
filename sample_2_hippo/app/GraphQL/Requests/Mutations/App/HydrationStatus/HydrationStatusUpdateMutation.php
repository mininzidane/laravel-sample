<?php

namespace App\GraphQL\Requests\Mutations\App\HydrationStatus;

use Closure;
use Exception;
use Illuminate\Validation\Rule;
use App\Models\HydrationStatus;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\HippoGraphQLActionCodes;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class HydrationStatusUpdateMutation extends AppHippoMutation
{
	protected $model = HydrationStatus::class;

	protected $permissionName = "Hydration Statuses: Update";

	protected $attributes = [
		"name" => "HydrationStatusUpdate",
		"model" => HydrationStatus::class,
	];

	protected $actionId = HippoGraphQLActionCodes::HYDRATIONSTATUS_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "The label must not be blank",
			"input.name.unique" => "The label must be unique",
			"input.abbreviation.unique" => "A abbreviation must be unique",
		];
	}

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
			],
			"input" => [
				"type" => GraphQL::type("HydrationStatusUpdateInput"),
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
					request()->header("Subdomain") .
						".tblHydrationStatusOptions",
					"label",
				)
					->where(function ($query) use ($args) {
						return $query
							->where("label", $args["input"]["name"])
							->where("deleted_at", null);
					})
					->ignore($args["id"]),
			],
			"input.abbreviation" => Rule::unique(
				request()->header("Subdomain") . ".tblHydrationStatusOptions",
				"abbr",
			)
				->where(function ($query) use ($args) {
					return $query
						->where("abbr", $args["input"]["abbreviation"])
						->where("deleted_at", null);
				})
				->ignore($args["id"]),
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
		// If the new label (name) exist on a deleted record, append the id so we can use it on the new one.
		$existingLabel = $this->model
			::on($this->subdomainName)
			->where("label", $args["input"]["name"])
			->onlyTrashed()
			->first();
		if ($existingLabel) {
			$existingLabel->update([
				"label" => $existingLabel->label . "_" . $existingLabel->id,
			]);
		}

		// If the new abbr (abbreviation) exist on a deleted record, append the id so we can use it on the new one.
		$existingAbbr = $this->model
			::on($this->subdomainName)
			->where("abbr", $args["input"]["abbreviation"])
			->onlyTrashed()
			->first();
		if ($existingAbbr) {
			$existingAbbr->update([
				"abbr" => $existingAbbr->abbr . "_" . $existingAbbr->id,
			]);
		}

		// Fixing column name issue - better solution to come
		$args["input"]["label"] = $args["input"]["name"];
		unset($args["input"]["name"]);
		$args["input"]["abbr"] = $args["input"]["abbreviation"];
		unset($args["input"]["abbreviation"]);

		$modelInstance->update($args["input"]);

		$this->affectedId = $args["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
