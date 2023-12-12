<?php

namespace App\GraphQL\Requests\Mutations\App\HydrationStatus;

use Closure;
use Illuminate\Validation\Rule;
use App\Models\HydrationStatus;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class HydrationStatusCreateMutation extends AppHippoMutation
{
	protected $model = HydrationStatus::class;

	protected $permissionName = "Hydration Statuses: Create";

	protected $attributes = [
		"name" => "HydrationStatusCreate",
		"model" => HydrationStatus::class,
	];

	protected $actionId = HippoGraphQLActionCodes::HYDRATIONSTATUS_CREATE;

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
			"input" => [
				"type" => GraphQL::type("HydrationStatusCreateInput"),
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
				)->where(function ($query) use ($args) {
					return $query
						->where("label", $args["input"]["name"])
						->where("deleted_at", null);
				}),
			],
			"input.abbreviation" => Rule::unique(
				request()->header("Subdomain") . ".tblHydrationStatusOptions",
				"abbr",
			)->where(function ($query) use ($args) {
				return $query
					->where("abbr", $args["input"]["abbreviation"])
					->where("deleted_at", null);
			}),
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

		$this->affectedId = $modelInstance->create($args["input"])->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
