<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemKitItemGraphQLType;
use App\GraphQL\Types\ItemSpeciesRestrictionGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemSpeciesRestrictionInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemSpeciesRestrictionInput",
		"description" => "species restriction for an item",
	];

	protected $graphQLType = ItemSpeciesRestrictionGraphQLType::class;
	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
				"description" => "id of the restriction",
			],
		];
	}
}
