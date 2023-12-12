<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemKitItemGraphQLType;
use App\GraphQL\Types\ItemTaxGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemTaxInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemTaxInput",
		"description" => "Tax for an item",
	];

	protected $graphQLType = ItemTaxGraphQLType::class;
	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
				"description" => "id of the tax",
			],
		];
	}
}
