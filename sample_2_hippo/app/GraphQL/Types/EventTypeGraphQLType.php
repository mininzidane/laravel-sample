<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\Models\EventType;
use GraphQL\Type\Definition\Type;

class EventTypeGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "eventType";

	protected $attributes = [
		"name" => "AppointmentType",
		"description" => "A scheduling appointment",
		"model" => EventType::class,
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
				"description" => "Event type name",
			],
			"description" => [
				"type" => Type::string(),
				"description" => "Event type description",
			],
			"appointments" => (new AppointmentField([
				"isList" => true,
			]))->toArray(),
		];
	}
}
