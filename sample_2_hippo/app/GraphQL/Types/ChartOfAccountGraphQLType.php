<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AccountCategoryField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\Models\ChartOfAccounts;
use GraphQL\Type\Definition\Type;

class ChartOfAccountGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "chartOfAccount";

	protected $attributes = [
		"name" => "ChartOfAccount",
		"description" =>
			"Chart of Accounts is the standard for classifying and aggregating revenue, expense, and balance sheet accounts in small-animal veterinary practice",
		"model" => ChartOfAccounts::class,
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
				"description" => "The descriptive name of the chart of account",
				"rules" => ["max:255"],
			],
			"series" => [
				"type" => Type::string(),
				"description" => "Account identifier",
				"rules" => ["max:191"],
			],
			"accountCategory" => (new AccountCategoryField([
				"description" =>
					"The account category for this chart of accounts",
			]))->toArray(),
			"items" => (new ItemField([
				"isList" => true,
				"description" =>
					"The items configured for this chart of accounts",
			]))->toArray(),
			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
				"description" =>
					"The invoice items configured for this chart of accounts",
			]))->toArray(),
		];
	}
}
