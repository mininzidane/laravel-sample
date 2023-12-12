<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\SpeciesField;
use App\Models\ItemSpeciesRestriction;
use GraphQL\Type\Definition\Type;

class ItemSpeciesRestrictionGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemSpeciesRestriction";

	protected $attributes = [
		"name" => "ItemSpeciesRestriction",
		"description" =>
			"Restrictions placed on which species an item can be used for",
		"model" => ItemSpeciesRestriction::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"item" => (new ItemField([
				"description" =>
					"Which item is restricted for the associated species",
			]))->toArray(),
			"species" => (new SpeciesField([
				"description" =>
					"Which species is restricted for the associated item",
			]))->toArray(),
		];
	}
}
