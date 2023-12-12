<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\PermissionField;
use App\Models\Authorization\Role;
use GraphQL\Type\Definition\Type;

class RoleGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "role";

	protected $attributes = [
		"name" => "Role",
		"description" => "A role for a medication",
		"model" => Role::class,
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
				"description" => "The name of the role",
			],
			"guard" => [
				"type" => Type::string(),
				"description" => "The guard that the role applies for",
				"alias" => "guard_name",
			],
			"permissions" => new PermissionField([
				"isList" => true,
				"description" => "The permissions associated with this role",
			]),
		];
	}
}
