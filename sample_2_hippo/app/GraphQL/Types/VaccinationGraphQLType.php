<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\ItemLegacyField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\UserField;
use App\Models\Vaccination;
use GraphQL\Type\Definition\Type;

class VaccinationGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "vaccine";

	protected $attributes = [
		"name" => "Vaccination",
		"description" => "A single vaccination for a patient",
		"model" => Vaccination::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id for the vaccine",
			],
			"currentGender" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "current_gender",
			],
			"currentWeight" => [
				"type" => Type::string(),
				"description" => "",
				"alias" => "current_weight",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "Name of vaccination at time it was given",
				"alias" => "vaccine_name",
			],
			"administeredAt" => [
				"type" => Type::string(),
				"description" => "The date the vaccination was administered",
				"alias" => "administered_date",
			],
			"administeredDate" => [
				"type" => Type::string(),
				"description" => "The date the vaccination was administered",
				"alias" => "administered_date",
			],
			"dosage" => [
				"type" => Type::float(),
				"description" => "The quantity of the vaccine given",
			],
			"lotNumber" => [
				"type" => Type::string(),
				"description" => "Lot the vaccine was pulled from",
				"alias" => "receiving_item_lot_number",
			],
			"serialNumber" => [
				"type" => Type::string(),
				"description" => "The serial number of the vaccine",
				"alias" => "serialnumber",
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "The vaccine expiration date",
				"alias" => "receiving_item_expiration_date",
			],
			"patient" => (new PatientField())->toArray(),
			"item" => (new ItemField())->toArray(),
			"invoice" => (new InvoiceField())->toArray(),
			"invoiceItem" => (new InvoiceItemField())->toArray(),
			"administeredBy" => (new UserField())->toArray(),
			"locationAdministered" => (new LocationField())->toArray(),
			"provider" => (new UserField())->toArray(),
		];
	}
}
