<?php

namespace App\GraphQL\Requests\Mutations\App\Credit;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Credit;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreditUpdateMutation extends AppHippoMutation
{
	protected $model = Credit::class;

	protected $permissionName = "Credits: Update";

	protected $attributes = [
		"name" => "CreditUpdateMutation",
	];

	protected $actionId = HippoGraphQLActionCodes::CREDIT_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::string(),
			],
			"input" => [
				"type" => GraphQL::type("CreditUpdateInput"),
			],
		];
	}

	protected function rules(array $args = []): array
	{
		return [
			"input.number" => [
				"max:65",
				"unique:" .
				request()->header("Subdomain") .
				".credits,number," .
				$args["id"] .
				",id",
			],
		];
	}

	function generate_id()
	{
		$data = random_bytes(16);

		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10

		return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.number.unique" => "The value must be unique or blank",
			"input.number.max" => "The value must be less than 65 characters",
			"input.currentValue.gte" => "The value must not be less than zero",
			"input.owner.required_if" =>
				"If account credit is chosen an owner must be selected",
			"input.type.in" => "Please add a type",
			"input.type.required" => "Card type required",
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
		$this->prepareResolve($args);

		//If card number not assigned give it one
		if (empty($args["input"]["number"])) {
			$args["input"]["number"] = $this->generate_id();
		}

		$modelInstance = $this->model
			::on($this->subdomainName)
			->find($args["id"]);

		if (!$modelInstance) {
			throw new Exception(
				"Cannot edit non-existent item: " . $args["id"],
			);
		}

		$modelInstance->fill($args["input"]);
		$modelInstance->save();

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
