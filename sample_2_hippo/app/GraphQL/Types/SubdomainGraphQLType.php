<?php

namespace App\GraphQL\Types;

use App\Models\Authorization\Subdomain;
use GraphQL\Type\Definition\Type;

class SubdomainGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "subdomain";

	protected $attributes = [
		"name" => "Subdomain",
		"description" => "A subdomain",
		"model" => Subdomain::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the subdomain",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The subdomain name",
			],
			"active" => [
				"type" => Type::boolean(),
				"description" => "Whether the subdomain is considered active",
			],
		];
	}
}
