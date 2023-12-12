<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ChartOfAccountsCategory;

class ChartOfAccountsCategoryQuery extends ApiHippoQuery
{
	protected $model = ChartOfAccountsCategory::class;

	protected $permissionName = "GraphQL: View Chart of Accounts Category";

	protected $attributes = [
		"name" => "chartOfAccountsCategoryQuery",
	];

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if (request()->cookie("_subdomain")) {
			$args["subdomain"] = request()->header("subdomain");
		}

		if (!array_key_exists("parent_category_id", $args)) {
			$args["parent_category_id"] = null;
		}

		$subdomainName = $args["subdomain"];
		$this->connectToSubdomain($subdomainName);

		$query = ChartOfAccountsCategory::on($subdomainName)->select(
			"id",
			"name",
			"parent_category_id",
		);
		if (isset($args["id"])) {
			$query->where("id", $args["id"]);
		} else {
			if (is_null($args["parent_category_id"])) {
				$query->where("parent_category_id");
			} else {
				$query->where(
					"parent_category_id",
					$args["parent_category_id"],
				);
			}
		}
		return $query->orderBy("name")->paginate();
	}
}
