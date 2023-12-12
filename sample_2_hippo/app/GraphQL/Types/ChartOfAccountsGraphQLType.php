<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AccountCategoryField;
use App\Models\ChartOfAccounts;
use GraphQL\Type\Definition\Type;

class ChartOfAccountsGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "chartOfAccounts";

	protected $attributes = [
		"name" => "ChartOfAccounts",
		"description" => "Available Chart of Accounts",
		"model" => ChartOfAccounts::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "The id of the chart of accounts item",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The chart of accounts item name",
			],

			"series" => [
				"type" => Type::string(),
				"description" => "Series number of the chart",
			],
			"category_id" => ["type" => Type::string()],

			"category" => (new AccountCategoryField())->toArray(),
		];
	}
}
