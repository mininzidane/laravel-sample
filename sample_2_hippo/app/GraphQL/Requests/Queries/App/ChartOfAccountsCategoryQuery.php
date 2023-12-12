<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ChartOfAccountsCategory;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class ChartOfAccountsCategoryQuery extends AppHippoQuery
{
	protected $model = ChartOfAccountsCategory::class;

	protected $permissionName = "Account Categories: Read";

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
