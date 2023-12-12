<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\UserField;
use App\Models\Resource;
use GraphQL\Type\Definition\Type;

class ResourceGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "resource";

	protected $attributes = [
		"name" => "Resource",
		"description" => "A scheduling resource",
		"model" => Resource::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"description" => [
				"type" => Type::string(),
				"description" => "A description of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the resource location",
			],
			"user" => (new UserField())->toArray(),
			"location" => (new LocationField([
				"description" => "Associated Location",
			]))->toArray(),
			"appointments" => (new AppointmentField([
				"isList" => true,
				"description" => "Associated Appointments",
			]))->toArray(),
		];
	}
}
