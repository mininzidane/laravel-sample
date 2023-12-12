<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemField;
use App\Models\ItemReplaces;
use GraphQL\Type\Definition\Type;

class ItemReplacesGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "itemReplaces";

	protected $attributes = [
		"name" => "ItemReplaces",
		"description" =>
			"Configuration details for which new item replaces another after certain changes",
		"model" => ItemReplaces::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item",
			],
			"replaces" => (new ItemField([
				"description" => "The item that replaces the previous item",
			]))->toArray(),
			"replaced" => (new ItemField([
				"description" => "The item that is replaced",
			]))->toArray(),
		];
	}
}
