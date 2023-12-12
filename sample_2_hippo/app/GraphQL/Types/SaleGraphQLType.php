<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\DispensationField;
use App\GraphQL\Fields\ItemLegacyField;
use App\GraphQL\Fields\LineItemField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OwnerField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\PaymentField;
use App\GraphQL\Fields\SaleStatusField;
use App\GraphQL\Fields\UserField;
use App\Models\Sale;
use GraphQL\Type\Definition\Type;

class SaleGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "sale";

	protected $attributes = [
		"name" => "Sale",
		"description" => "A sale",
		"model" => Sale::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id for the sale",
				"alias" => "sale_id",
			],
			"completedAt" => [
				"type" => Type::string(),
				"description" => "When the sale was completed",
				"alias" => "sale_completed_time",
			],
			"comment" => [
				"type" => Type::string(),
				"description" => "Any additional details about the sale",
			],
			"payment" => [
				"type" => Type::string(),
				"description" =>
					"Combined description of the payment type and quantity",
				"alias" => "payment_type",
			],
			"total" => [
				"type" => Type::string(),
				"description" => "The total cost associated with the sale",
				"alias" => "updated_total",
			],
			"rounding" => [
				"type" => Type::string(),
				"description" => "Nearest fractional value to round to",
			],
			"employee" => (new UserField([
				"description" => "The user that created the sale",
			]))->toArray(),
			"owner" => (new OwnerField([
				"description" => "The owner associated with the sale",
			]))->toArray(),
			"location" => (new LocationField([
				"description" => "The location at which the sale was made",
			]))->toArray(),
			"patient" => (new PatientField([
				"description" => "The patient associated with the sale",
			]))->toArray(),
			"status" => (new SaleStatusField([
				"alias" => "status",
				"description" => "Sale Status",
			]))->toArray(),
			"lineItems" => (new LineItemField([
				"isList" => true,
				"description" => "Line items for the sale",
			]))->toArray(),
			"items" => (new ItemLegacyField([
				"isList" => true,
				"description" => "Inventory items for the sale",
			]))->toArray(),
			"dispensations" => (new DispensationField([
				"isList" => true,
				"description" => "Dispensations included in this sale",
			]))->toArray(),
			"payments" => (new PaymentField([
				"isList" => true,
				"description" => "The payments applied to this sale",
			]))->toArray(),
		];
	}
}
