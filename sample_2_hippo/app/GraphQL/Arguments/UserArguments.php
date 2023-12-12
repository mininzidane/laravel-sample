<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\UserResolver;
use GraphQL\Type\Definition\Type;

class UserArguments extends AdditionalArguments
{
	public static $resolver = UserResolver::class;

	public function getArguments()
	{
		return [
			"email" => [
				"name" => "email",
				"type" => Type::string(),
			],
			"isProvider" => [
				"name" => "isProvider",
				"type" => Type::boolean(),
			],
			"username" => [
				"name" => "username",
				"type" => Type::string(),
			],
			"location" => [
				"name" => "location",
				"type" => Type::string(),
			],
			"active" => [
				"name" => "active",
				"type" => Type::boolean(),
			],
			"email_verified" => [
				"name" => "emailVerified",
				"type" => Type::boolean(),
			],
			"fullName" => [
				"name" => "fullName",
				"type" => Type::string(),
			],
			"firstName" => [
				"name" => "firstName",
				"type" => Type::string(),
			],
			"lastName" => [
				"name" => "lastName",
				"type" => Type::string(),
			],
			"removed" => [
				"name" => "removed",
				"type" => Type::int(),
			],
			"specialty" => [
				"name" => "specialty",
				"type" => Type::string(),
			],
			"degree" => [
				"name" => "degree",
				"type" => Type::string(),
			],
			"role" => [
				"name" => "role",
				"type" => Type::int(),
			],
		];
	}
}
