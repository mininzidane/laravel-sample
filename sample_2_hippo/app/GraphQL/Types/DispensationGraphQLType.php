<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\SaleField;
use App\GraphQL\Fields\UserField;
use App\Models\Dispensation;
use GraphQL\Type\Definition\Type;

class DispensationGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "dispensation";

	protected $attributes = [
		"name" => "Dispensation",
		"description" => "A dispensation for a medication",
		"model" => Dispensation::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"line" => [
				"type" => Type::string(),
				"description" =>
					"The line of the sale associated with this dispensation",
			],
			"issueDate" => [
				"type" => Type::string(),
				"description" => "The date this dispensation was issued",
				"alias" => "issue_date",
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" => "The date this dispensation was issued",
				"alias" => "expiration_date",
			],
			"units" => [
				"type" => Type::string(),
				"description" => "The unit that was dispensed",
			],
			"quantity" => [
				"type" => Type::string(),
				"description" => "The quantity dispensed",
				"alias" => "qty",
			],
			"note" => [
				"type" => Type::string(),
				"description" =>
					"Any associated comments about this dispensation",
			],
			"signed" => [
				"type" => Type::boolean(),
				"description" => "Is the dispensation signed",
			],
			"onEstimate" => [
				"type" => Type::boolean(),
				"description" => "Does this dispensation appear on an estimate",
				"alias" => "on_estimate",
			],
			"originallySignedAt" => [
				"type" => Type::string(),
				"description" => "The associated with this dispensation",
				"alias" => "signed_time_original",
			],
			"lastSignedAt" => [
				"type" => Type::string(),
				"description" => "The associated with this dispensation",
				"alias" => "signed_time_last",
			],
			"user" => (new UserField())->toArray(),
			"signedByOriginal" => (new UserField())->toArray(),
			"signedByLast" => (new UserField())->toArray(),
			"prescription" => (new PrescriptionField())->toArray(),
			"sale" => (new SaleField([
				"description" => "The sale associated with this dispensation",
			]))->toArray(),
			"location" => (new LocationField([
				"description" => "The location for this dispensation",
			]))->toArray(),
		];
	}
}
