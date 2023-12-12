<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\LocationField;
use App\Models\ItemLocation;
use GraphQL\Type\Definition\Type;

class ItemLocationGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemLocation";

	protected $attributes = [
		"name" => "ItemLocation",
		"description" =>
			"Configuration of which items exist at which practice location",
		"model" => ItemLocation::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item",
			],
			"item" => (new ItemField([
				"description" =>
					"An item that exists at the associated location",
			]))->toArray(),
			"location" => (new LocationField([
				"description" =>
					"The location that the associated item is available at",
			]))->toArray(),
		];
	}
}
