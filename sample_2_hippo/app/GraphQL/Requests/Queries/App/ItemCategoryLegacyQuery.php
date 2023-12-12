<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemLegacy;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class ItemCategoryLegacyQuery extends AppHippoQuery
{
	protected $model = ItemLegacy::class;

	protected $permissionName = "Legacy Item Categories: Read";

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
