<?php

namespace App\GraphQL\InputObjects\Item;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ItemVolumePricingGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemVolumePricingInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemVolumePricingInput",
		"description" => "Pricing discount by volume",
	];

	protected $requiredFields = ["quantity", "unitPrice"];
	protected $graphQLType = ItemVolumePricingGraphQLType::class;
	protected $inputObject = true;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
				"description" => "id of the tax",
			],
			"quantity" => [
				"name" => "quantity",
				"type" => Type::int(),
				"description" => "The name of the tax",
			],
			"unitPrice" => [
				"name" => "unitPrice",
				"type" => Type::float(),
				"description" => "Tax percentage",
			],
		];
	}
}
