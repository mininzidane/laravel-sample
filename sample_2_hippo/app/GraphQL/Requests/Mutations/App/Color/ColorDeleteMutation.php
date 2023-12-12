<?php

namespace App\GraphQL\Requests\Mutations\App\Color;

use Closure;
use App\Models\Color;
use GraphQL\Type\Definition\Type;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class ColorDeleteMutation extends AppHippoMutation
{
	protected $model = Color::class;

	protected $permissionName = "Colors: Delete";

	protected $attributes = [
		"name" => "ColorDelete",
		"model" => Color::class,
	];

	protected $actionId = HippoGraphQLActionCodes::COLOR_DELETE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"id" => [
				"type" => Type::int(),
				"rules" => ["required"],
			],
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
