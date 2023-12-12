<?php

namespace App\GraphQL\Requests\Mutations\App\Credit;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Credit;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreditCreateMutation extends AppHippoMutation
{
	protected $model = Credit::class;

	protected $permissionName = "Credits: Create";

	protected $attributes = [
		"name" => "CreditCreateInput",
		"model" => Credit::class,
	];

	protected $actionId = HippoGraphQLActionCodes::CREDIT_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.number.unique" =>
				"The value must be blank or unique (may have been deleted)",
			"input.number.max" => "The value must be less than 65 characters",
			"input.originalValue.gte" => "The value must not be less than zero",
			"input.owner.required_if" =>
				"If account credit is chosen an owner must be selected",
			"input.type.in" => "Please add a type",
			"input.type.required" => "Card type required",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("CreditCreateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.number" => [
				"max:65",
				"unique:" . request()->header("Subdomain") . ".credits,number",
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		//If card number not assigned give it one
		if (empty($args["input"]["number"])) {
			$args["input"]["number"] = Credit::generate_id();
		}

		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$this->affectedId = $modelInstance->create($args["input"])->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
