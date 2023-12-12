<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Authorization\Subdomain;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;

class SubdomainQuery extends ApiHippoQuery
{
	protected $model = Subdomain::class;

	protected $permissionName = "GraphQL: View Available Subdomains";

	protected $attributes = [
		"name" => "paymentTypeQuery",
	];

	public function authorize(
		$root,
		array $args,
		$ctx,
		ResolveInfo $resolveInfo = null,
		Closure $getSelectFields = null
	): bool {
		$user = Auth::user();

		if (!$user->hasPermissionTo("GraphQL: Access Api", "api")) {
			return false;
		}

		if (
			!$user->hasPermissionTo("GraphQL: View Available Subdomains", "api")
		) {
			return false;
		}

		return true;
	}

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$permissions = Auth::user()->permissions;

		$permissions = $permissions->load("subdomain");

		$subdomains = [];

		foreach ($permissions as $permission) {
			if (!$permission->subdomain) {
				continue;
			}

			$subdomains[] = $permission->subdomain->id;
		}

		$limit = array_key_exists("limit", $args) ? $args["limit"] : 10;
		$page = array_key_exists("page", $args) ? $args["page"] : 1;

		return Subdomain::whereIn("id", $subdomains)->paginate(
			$limit,
			["*"],
			"page",
			$page,
		);
	}
}
