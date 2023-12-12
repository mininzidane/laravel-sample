<?php

namespace App\GraphQL\Requests\Mutations\App\Markings;

use Closure;
use App\Models\Markings;
use GraphQL\Type\Definition\Type;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class MarkingsDeleteMutation extends AppHippoMutation
{
	protected $model = Markings::class;

	protected $permissionName = "Markings: Delete";

	protected $attributes = [
		"name" => "MarkingsDelete",
		"model" => Markings::class,
	];

	protected $actionId = HippoGraphQLActionCodes::MARKINGS_DELETE;

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
				"rules" => ["required"],
			],
		];
	}

	public function validationErrorMessages(array $args = []): array
	{
		return [
			"id.required" => "An ID is required.",
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
			->findOrFail($args["id"]);
		$modelInstance->setConnection($this->subdomainName);

		$modelInstance->delete();

		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
