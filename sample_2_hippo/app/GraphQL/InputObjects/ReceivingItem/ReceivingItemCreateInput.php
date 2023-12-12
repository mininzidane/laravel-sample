<?php

namespace App\GraphQL\InputObjects\ReceivingItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingItemGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingItemCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingItemCreateInput",
		"description" => "The input object for creating a new receiving item",
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
			"item" => [
				"type" => Type::int(),
				"description" => "The id of the item to add to the receiving",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Item,id",
				],
			],
			"receiving" => [
				"type" => Type::int(),
				"description" =>
					"The id of the receiving to assign to this receiving item",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Receiving,id",
				],
			],
			"quantity" => [
				"type" => Type::float(),
				"description" => "The quantity of this receiving item",
				"default" => 1,
				"rules" => ["required", "min:0"],
			],
		];
	}
}
