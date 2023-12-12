<?php

namespace App\GraphQL\InputObjects\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\InvoiceGraphQLType;
use GraphQL\Type\Definition\Type;

class InvoiceCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "invoiceCreateInput",
		"description" => "The input object for creating a new invoice",
	];

	protected $graphQLType = InvoiceGraphQLType::class;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			//TODO looks a bit like a remnant but will need to changed in the front end to get rid of it
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"comment" => [
				"type" => Type::string(),
				"description" =>
					"A comment describing any additional information for this invoice",
			],
			"printComment" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether the comment is printed on invoices",
				"alias" => "print_comment",
			],
			"rounding" => [
				"type" => Type::float(),
				"description" =>
					"The currency denomination to round to. Defaults to nearest cent.",
				"rules" => ["numeric"],
			],
			"isTaxable" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether taxes are applied to this invoice",
				"alias" => "is_taxable",
			],
			"isActive" => [
				"type" => Type::boolean(),
				"description" =>
					"Flag that determines whether invoice is active",
				"alias" => "active",
			],
			"total" => [
				"type" => Type::float(),
				"description" => "The final total for the invoice",
				"rules" => ["numeric"],
			],
			"totalPayments" => [
				"type" => Type::float(),
				"description" =>
					"The total of all applied payments for this invoice",
				"rules" => ["numeric"],
			],
			"amountDue" => [
				"type" => Type::float(),
				"description" => "The amount left due on the invoice",
				"rules" => ["numeric"],
			],
			"active" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the invoice is the most recently active invoice for a given patient",
			],
			"completedAt" => [
				"type" => Type::string(),
				"description" => "When the invoice is paid in full",
				"alias" => "completed_at",
				"rules" => ["date"],
			],
			"patient" => [
				"type" => Type::int(),
				"description" =>
					"The id of the patient to assign to this invoice",
				"relation" => true,
				"default" => null,
				"alias" => "patient_id",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Patient,id",
				],
			],
			"owner" => [
				"type" => Type::int(),
				"description" =>
					"The id of the owner to assign to this invoice",
				"relation" => true,
				"default" => null,
				"alias" => "owner_id",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Owner,id",
				],
			],
			"location" => [
				"type" => Type::int(),
				"description" =>
					"The id of the location this invoice was generated at",
				"relation" => true,
				"default" => null,
				"alias" => "location_id",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Location,id",
				],
			],
			"user" => [
				"type" => Type::int(),
				"description" => "The id of the user who created this invoice",
				"relation" => true,
				"default" => null,
				"alias" => "user_id",
				"rules" => ["exists:" . $subdomainName . "App\Models\User,id"],
			],
		];
	}
}
