<?php

namespace App\GraphQL\Requests\Mutations\App\ItemCategory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\ItemCategory;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ItemCategoryCreateMutation extends AppHippoMutation
{
	protected $model = ItemCategory::class;

	protected $permissionName = "Item Categories: Create";

	protected $attributes = [
		"name" => "ItemCategoryCreate",
		"model" => ItemCategory::class,
	];

	protected $actionId = HippoGraphQLActionCodes::ITEM_CATEGORY_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.unique" => "The name must be unique",
			"input.name.required" => "The value must not be blank",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ItemCategoryCreateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => [
				"required",
				"max:255",
				"unique:" .
				request()->header("Subdomain") .
				".item_categories,name",
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
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$id = $modelInstance->create($this->args["input"])->id;

		$this->affectedId = $id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $id)
			->paginate(1);
	}
}
