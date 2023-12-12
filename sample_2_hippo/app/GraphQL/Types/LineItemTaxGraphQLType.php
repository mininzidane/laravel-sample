<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ItemLegacyField;
use App\GraphQL\Fields\LineItemField;
use App\GraphQL\Fields\SaleField;
use App\Models\LineItemTax;
use GraphQL\Type\Definition\Type;

class LineItemTaxGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "lineItemTax";

	protected $attributes = [
		"name" => "LineItemTax",
		"description" => "Taxes for an item",
		"model" => LineItemTax::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the item",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The human readable description of the tax",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"percent" => [
				"type" => Type::string(),
				"description" => "The tax percentage for this tax",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"line" => [
				"type" => Type::int(),
				"description" => "The line number that was taxed at this rate",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			],
			"item" => (new ItemLegacyField([
				"description" => "The item this tax applies to",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
			"sale" => (new SaleField([
				"description" => "The sale using this line item tax",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
			"lineItem" => (new LineItemField([
				"description" => "Which sale has been taxed",
				"deprecationReason" =>
					'This field has been deprecated and will be removed in future versions. Please use the "itemTaxes Object". The LineItemTax Object is only used for Hippo 1 users on api.hippo.vet.',
			]))->toArray(),
		];
	}
}
