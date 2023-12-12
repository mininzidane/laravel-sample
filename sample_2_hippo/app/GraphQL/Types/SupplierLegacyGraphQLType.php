<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\ReceivingLegacyField;
use App\GraphQL\Fields\UserField;
use App\Models\SupplierLegacy;
use GraphQL\Type\Definition\Type;

class SupplierLegacyGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "supplierLegacy";

	protected $attributes = [
		"name" => "SupplierLegacy",
		"description" => "An item supplier",
		"model" => SupplierLegacy::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the supplier",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The company name",
				"alias" => "company_name",
			],
			"accountNumber" => [
				"type" => Type::string(),
				"description" => "The practice account number for the supplier",
				"alias" => "account_number",
			],
			"user" => (new UserField([
				"description" => "The user who configured this supplier",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" =>
					"The organization this supplier is configured for",
			]))->toArray(),
			"receivings" => (new ReceivingLegacyField([
				"isList" => true,
				"description" => "The receivings associated with this supplier",
			]))->toArray(),
		];
	}
}
