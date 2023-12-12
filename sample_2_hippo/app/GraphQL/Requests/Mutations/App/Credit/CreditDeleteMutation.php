<?php

namespace App\GraphQL\Requests\Mutations\App\Credit;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Credit;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;

class CreditDeleteMutation extends AppHippoMutation
{
	protected $model = Credit::class;

	protected $permissionName = "Credits: Delete";

	protected $attributes = [
		"name" => "CreditDelete",
	];

	protected $actionId = HippoGraphQLActionCodes::CREDIT_DELETE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.required" => "ID is required",
		];
	}

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::int(),
				"rules" => ["required"],
			],
		];
	}

	public function __construct()
	{
		return parent::__construct();
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
		$modelInstance = $this->model
			::on($this->subdomainName)
			->findOrFail($args["id"]);

		$modelInstance->delete();

		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
