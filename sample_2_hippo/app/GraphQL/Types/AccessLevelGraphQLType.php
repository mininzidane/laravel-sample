<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\PermissionField;
use App\Models\AccessLevel;
use GraphQL\Type\Definition\Type;

class AccessLevelGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "accessLevel";

	protected $attributes = [
		"name" => "AccessLevel",
		"description" => "A role for a user",
		"model" => AccessLevel::class,
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
