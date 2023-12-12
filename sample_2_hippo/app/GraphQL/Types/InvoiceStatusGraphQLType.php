<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceField;
use App\Models\InvoiceStatus;
use GraphQL\Type\Definition\Type;

class InvoiceStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "invoiceStatus";

	protected $attributes = [
		"name" => "InvoiceStatus",
		"description" => "Possible statuses for invoices",
		"model" => InvoiceStatus::class,
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
				"description" =>
					"The human-readable name of the payment method",
				"rules" => ["max:191"],
			],
			"invoices" => (new InvoiceField([
				"isList" => true,
				"description" => "Invoices with this invoice status",
			]))->toArray(),
		];
	}
}
