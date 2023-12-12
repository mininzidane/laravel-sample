<?php

namespace App\GraphQL\InputObjects\User;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\UserGraphQLType;
use GraphQL\Type\Definition\Type;

class UserDeleteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "userDeleteInput",
		"description" => "The input object for deleting a user",
	];
	protected $graphQLType = UserGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();
		return [
			"id" => [
				"type" => Type::int(),
				"description" => "The id of the user to delete",
				"default" => null,
				"rules" => [
					"required",
					"exists: " . $subdomainName . "App\Models\User,id",
				],
			],
		];
	}
}
