<?php

namespace App\GraphQL\Types;

use App\Models\Authorization\Permission;
use GraphQL\Type\Definition\Type;

class PermissionGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "permission";

	protected $attributes = [
		"name" => "Permission",
		"description" => "A permission for a medication",
		"model" => Permission::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the permission",
			],
			"guard" => [
				"type" => Type::string(),
				"description" => "The guard that the permission applies for",
				"alias" => "guard_name",
			],
		];
	}
}
