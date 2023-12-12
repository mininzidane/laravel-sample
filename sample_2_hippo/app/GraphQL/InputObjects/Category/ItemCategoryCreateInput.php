<?php

namespace App\GraphQL\InputObjects\Category;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Types\ItemCategoryGraphQLType;
use App\GraphQL\InputObjects\HippoInputType;
use GraphQL\Type\Definition\Type;

class ItemCategoryCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "itemCategoryCreateInput",
		"description" => "Item Category for CRUD operations",
	];

	protected $requiredFields = ["name"];
	protected $graphQLType = ItemCategoryGraphQLType::class;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item category",
			],
			"name" => [
				"name" => "name",
				"type" => Type::string(),
				"description" => "The name of the item category",
			],
		];
	}
}
