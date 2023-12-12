<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\LocationResolver;
use GraphQL\Type\Definition\Type;

class LocationsArguments extends AdditionalArguments
{
	public static $resolver = LocationResolver::class;

	public function getArguments()
	{
		return [
			"locations" => [
				"name" => "locations",
				"type" => Type::string(),
			],
			"location_id" => [
				"name" => "location_id",
				"type" => Type::int(),
			],
			"name" => [
				"name" => "name",
				"type" => Type::string(),
			],
			"email" => [
				"name" => "email",
				"type" => Type::string(),
			],
			"city" => [
				"name" => "city",
				"type" => Type::string(),
			],
			"zip" => [
				"name" => "zip",
				"type" => Type::string(),
			],
			"subregion" => [
				"name" => "subregion",
				"type" => Type::int(),
			],
		];
	}
}
