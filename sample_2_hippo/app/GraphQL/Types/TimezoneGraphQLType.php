<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\LocationField;
use App\Models\Timezone;
use GraphQL\Type\Definition\Type;

class TimezoneGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "timezone";

	protected $attributes = [
		"name" => "Timezone",
		"description" => "A practice\s Timezone",
		"model" => Timezone::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the location",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The human-readable name of the timezone",
				"alias" => "value",
			],
			"abbreviation" => [
				"type" => Type::string(),
				"description" => "The timezone abbreviation",
				"alias" => "abbr",
			],
			"offset" => [
				"type" => Type::string(),
				"description" => "Hour offset from UTC",
			],
			"php_supported" => [
				"type" => Type::string(),
				"description" => "Php supported timezone",
			],
			"locations" => (new LocationField(["isList" => true]))->toArray(),
		];
	}
}
