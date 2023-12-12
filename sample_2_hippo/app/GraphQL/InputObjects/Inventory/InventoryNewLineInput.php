<?php

namespace App\GraphQL\InputObjects\Inventory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InventoryGraphQLType;
use GraphQL\Type\Definition\Type;

class InventoryNewLineInput extends HippoInputType
{
	protected $attributes = [
		"name" => "inventoryNewLineInput",
		"description" =>
			"The input object for creating a new line in the inventory table",
	];

	protected $graphQLType = InventoryGraphQLType::class;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"itemId" => [
				"type" => Type::int(),
				"description" => "The id of the Items resource",
				"alias" => "item_id",
				"relation" => true,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Item,id",
				],
			],
			"locationId" => [
				"type" => Type::int(),
				"description" =>
					"The id of the location this invoice was generated at",
				"relation" => true,
				"default" => null,
				"alias" => "location_id",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Location,id",
				],
			],
			"statusId" => [
				"type" => Type::int(),
				"description" => "The status of the item",
				"alias" => "status_id",
			],
			"isOpen" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag indicating whether an associated contain is open or not",
				"alias" => "is_open",
			],
		];
	}
}
