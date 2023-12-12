<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemLegacy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class ItemCategoryLegacyQuery extends ApiHippoQuery
{
	protected $model = ItemLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Item Categories";

	protected $attributes = [
		"name" => "itemCategoryQuery",
	];

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$subdomainName = $args["subdomain"];

		$this->connectToSubdomain($subdomainName);

		return ItemLegacy::on($subdomainName)
			->select("category as name")
			->groupBy("category")
			->paginate();
	}
}
