<?php

namespace App\GraphQL\InputObjects\ReceivingItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingItemGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingItemUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingItemUpdateInput",
		"description" =>
			"The input object for updating an existing receiving item",
	];

	protected $graphQLType = ReceivingItemGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::int(),
				"description" => "The id of the receiving item to update",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\ReceivingItem,id",
				],
			],
			"quantity" => [
				"type" => Type::float(),
				"description" => "The quantity of this receiving item",
			],
			"costPrice" => [
				"type" => Type::float(),
				"description" =>
					"The price of the items when they were purchased",
				"rules" => ["numeric", "gte:0"],
				"alias" => "cost_price",
			],
			"discountPercentage" => [
				"type" => Type::float(),
				"description" => "Any discount applied to the item",
				"rules" => ["numeric"],
				"alias" => "discount_percentage",
			],
			"lotNumber" => [
				"type" => Type::string(),
				"description" => "The lot number of the received item",
				"default" => null,
				"rules" => [],
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" => "The serial number of the received item",
				"default" => null,
				"rules" => [],
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "The date the received item expires",
				"default" => null,
				"rules" => [],
			],
		];
	}
}
