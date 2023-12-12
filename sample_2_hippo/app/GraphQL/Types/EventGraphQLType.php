<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\EventTypeField;
use App\Models\Event;
use GraphQL\Type\Definition\Type;

class EventGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "event";

	protected $attributes = [
		"name" => "Event",
		"description" => "A scheduling appointment",
		"model" => Event::class,
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
			"type" => (new EventTypeField([
				"description" => "The category of event this event falls under",
			]))->toArray(),
		];
	}
}
