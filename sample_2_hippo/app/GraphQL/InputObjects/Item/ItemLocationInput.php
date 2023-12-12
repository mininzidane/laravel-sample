<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemKitItemGraphQLType;
use App\GraphQL\Types\ItemLocationGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemLocationInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemLocationInput",
		"description" => "Location for an item",
	];

	protected $graphQLType = ItemLocationGraphQLType::class;
	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
				"description" => "id of the location",
			],
		];
	}
}
