<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InventoryField;
use App\Models\InventoryStatus;
use GraphQL\Type\Definition\Type;

class InventoryStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "inventoryStatus";

	protected $attributes = [
		"name" => "InventoryStatus",
		"description" => "Possible statuses for inventories",
		"model" => InventoryStatus::class,
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
				"description" =>
					"The human-readable name of the payment method",
				"rules" => ["max:191"],
			],
			"inventory" => (new InventoryField([
				"isList" => true,
				"description" => "Inventories with this inventory status",
			]))->toArray(),
		];
	}
}
