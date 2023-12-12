<?php

namespace App\GraphQL\InputObjects;

use App\GraphQL\Types\ItemVolumePricingGraphQLType;
use GraphQL\Type\Definition\Type;

class ItemVolumePricingInputOld extends HippoInputType
{
	protected $attributes = [
		"name" => "itemVolumePricingInputOld",
		"description" => "Pricing discount by volume",
	];

	protected $requiredFields = ["quantity", "unitPrice"];
	protected $graphQLType = ItemVolumePricingGraphQLType::class;

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
