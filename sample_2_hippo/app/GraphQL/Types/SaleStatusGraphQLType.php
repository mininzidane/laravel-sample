<?php

namespace App\GraphQL\Types;

use App\Models\SaleStatus;
use GraphQL\Type\Definition\Type;

class SaleStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "saleStatus";

	protected $attributes = [
		"name" => "SaleStatus",
		"description" => "A sale status",
		"model" => SaleStatus::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id for the sale",
				"alias" => "status_id",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the status",
				"alias" => "status_name",
			],
		];
	}
}
