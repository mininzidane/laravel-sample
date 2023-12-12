<?php

namespace App\GraphQL\InputObjects\InvoiceItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceItemGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceItemUpdateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceItemUpdateInput",
		"description" =>
			"The input object for updating an existing invoice item",
	];

	protected $graphQLType = InvoiceItemGraphQLType::class;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::int(),
				"description" => "The id of the invoice item to update",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\InvoiceItem,id",
				],
			],
			"chart" => [
				"type" => Type::int(),
				"description" =>
					"The id of the chart to assign to this invoice item",
				"default" => null,
				"alias" => "chart_id",
			],
			"chartType" => [
				"type" => Type::string(),
				"description" => "The type of chart to be associated",
				"default" => "soap",
				"alias" => "chart_type",
			],
			"description" => [
				"type" => Type::string(),
				"description" =>
					"The description of the item at the time it was added to the invoice",
			],
			"quantity" => [
				"type" => Type::float(),
				"description" =>
					"The quantity of the item being sold.  Supports decimals to 5 places.",
				"rules" => ["numeric"],
			],
			"price" => [
				"type" => Type::float(),
				"description" =>
					"The price of the item at the time it was added to the invoice",
				"rules" => ["numeric"],
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" =>
					"The date associated with this item being administered.",
				"alias" => "administered_date",
			],
			"discountPercent" => [
				"type" => Type::float(),
				"description" =>
					"The integer percentage that the item is discounted at the time it was added to the invoice",
				"alias" => "discount_percent",
				"rules" => ["numeric", "lte:100", "gte:0"],
			],
			"discountAmount" => [
				"type" => Type::float(),
				"description" =>
					"The flat currency amount the item is to be discounted at the time it was added to the invoice",
				"alias" => "discount_amount",
				"rules" => ["numeric"],
			],
			"unitPrice" => [
				"type" => Type::float(),
				"description" =>
					"The configured sale price for the item at the time it was added to the invoice",
				"alias" => "unit_price",
				"rules" => ["numeric"],
			],
			"dispensingFee" => [
				"type" => Type::float(),
				"description" =>
					"The fee added to the price of the item for dispensation at the time it was added to the invoice",
				"alias" => "dispensing_fee",
				"rules" => ["numeric"],
			],
			"hideFromRegister" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag for whether or not to display the item on the available items on the sale register at the time it was added to the invoice",
				"alias" => "hide_from_register",
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" => "The serial number of the item used",
				"default" => null,
				"alias" => "serial_number",
			],
			"allowExcessiveQuantity" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not quantities that would result in negative totals are allowed",
				"default" => 0,
			],
			"provider" => [
				"type" => Type::int(),
				"description" =>
					"The id of the provider to assign to this invoice item",
			],
		];
	}
}
