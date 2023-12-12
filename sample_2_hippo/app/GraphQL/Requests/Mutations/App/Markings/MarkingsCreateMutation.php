<?php

namespace App\GraphQL\Requests\Mutations\App\Markings;

use Closure;
use App\Models\Markings;
use Illuminate\Validation\Rule;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class MarkingsCreateMutation extends AppHippoMutation
{
	protected $model = Markings::class;

	protected $permissionName = "Markings: Create";

	protected $attributes = [
		"name" => "MarkingsCreate",
		"model" => Markings::class,
	];

	protected $actionId = HippoGraphQLActionCodes::MARKINGS_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.unique" =>
				"The name must be unique for a selected species",
			"input.name.required" => "The name must not be blank",
			"input.species.required" => "A species must be selected",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("MarkingsCreateInput"),
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
					request()->header("Subdomain") . ".tblMarkings",
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
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$marking = $modelInstance->withTrashed()->updateOrCreate(
			[
				"name" => $args["input"]["name"],
				"species" => $args["input"]["species"],
			],
			[
				"name" => $args["input"]["name"],
				"species" => $args["input"]["species"],
			],
		);

		if ($marking->trashed()) {
			$marking->restore();
		}

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
