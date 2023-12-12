<?php

namespace App\GraphQL\Requests\Mutations\App\Item;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Item;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class ItemUndeleteMutation extends AppHippoMutation
{
	protected $model = Item::class;

	protected $permissionName = "Items: Delete";

	protected $attributes = [
		"name" => "ItemUndelete",
		"model" => Item::class,
	];

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"itemIds" => [
				"type" => Type::listOf(Type::id()),
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
		Item::on($this->subdomainName)
			->whereIn("id", $this->args["itemIds"])
			->onlyTrashed()
			->restore();

		return Item::on($this->subdomainName)
			->whereIn("id", $this->args["itemIds"])
			->paginate(10);
	}
}
