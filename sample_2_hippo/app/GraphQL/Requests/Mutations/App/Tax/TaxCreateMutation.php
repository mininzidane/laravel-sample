<?php

namespace App\GraphQL\Requests\Mutations\App\Tax;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Tax;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TaxCreateMutation extends AppHippoMutation
{
	protected $model = Tax::class;

	protected $permissionName = "Taxes: Create";

	protected $attributes = [
		"name" => "TaxCreate",
		"model" => Tax::class,
	];

	protected $actionId = HippoGraphQLActionCodes::TAX_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.unique" => "The name must be unique",
			"input.name.required" => "The value must not be blank",
			"input.percent.required" => "The value must not be blank",
			"input.percent.gt" => "The value must be greater than zero",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("TaxCreateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => [
				"required",
				"max:255",
				"unique:" . request()->header("Subdomain") . ".taxes,name",
			],
			"input.percent" => ["required", "gt:0"],
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

		$this->affectedId = $modelInstance->create($args["input"])->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
