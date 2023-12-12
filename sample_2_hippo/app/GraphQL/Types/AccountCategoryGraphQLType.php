<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AccountCategoryField;
use App\GraphQL\Fields\ChartOfAccountField;
use App\Models\AccountCategory;
use GraphQL\Type\Definition\Type;

class AccountCategoryGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "accountCategory";

	protected $attributes = [
		"name" => "AccountCategory",
		"description" => "The account category information",
		"model" => AccountCategory::class,
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
				"description" => "The descriptive name of the account category",
				"rules" => ["max:255"],
			],
			"parentCategory" => (new AccountCategoryField([
				"description" => 'This account category\'s parent category',
			]))->toArray(),
			"childCategories" => (new AccountCategoryField([
				"isList" => true,
				"description" => "The subcategories for this category",
			]))->toArray(),
			"chartOfAccounts" => (new ChartOfAccountField([
				"isList" => true,
				"description" =>
					"The chart of accounts that are of this account type",
			]))->toArray(),
		];
	}
}
