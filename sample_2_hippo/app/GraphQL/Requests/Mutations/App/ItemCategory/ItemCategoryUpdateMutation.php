<?php

namespace App\GraphQL\Requests\Mutations\App\ItemCategory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\ItemCategory;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ItemCategoryUpdateMutation extends AppHippoMutation
{
	protected $model = ItemCategory::class;

	protected $permissionName = "Item Categories: Update";

	protected $attributes = [
		"name" => "ItemCategoryUpdate",
		"model" => ItemCategory::class,
	];

	protected $actionId = HippoGraphQLActionCodes::ITEM_CATEGORY_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "The value must not be blank",
			"input.name.unique" => "The value must be unique",
		];
	}

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::string(),
			],
			"input" => [
				"type" => GraphQL::type("ItemCategoryUpdateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => [
				"max:255",
				"unique:" .
				request()->header("Subdomain") .
				".item_categories,name," .
				$args["id"] .
				",id",
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
			->find($args["id"]);

		if (!$modelInstance) {
			throw new Exception(
				"Cannot update non-existent item: " . $args["id"],
			);
		}

		$modelInstance->fill($args["input"]);
		$modelInstance->save();

		$this->affectedId = $args["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
