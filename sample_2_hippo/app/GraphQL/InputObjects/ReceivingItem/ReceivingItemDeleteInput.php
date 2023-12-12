<?php

namespace App\GraphQL\InputObjects\ReceivingItem;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingItemGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingItemDeleteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingItemDeleteInput",
		"description" => "The input object for creating a new receiving item",
	];

	protected $graphQLType = ReceivingItemGraphQLType::class;

	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"receivingItem" => [
				"type" => Type::int(),
				"description" => "The id of the receiving item to delete",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\ReceivingItem,id",
				],
			],
		];
	}
}
