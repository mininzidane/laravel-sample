<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\DispensationField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\UserField;
use App\Models\Prescription;
use GraphQL\Type\Definition\Type;

class PrescriptionGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "prescription";

	protected $attributes = [
		"name" => "Prescription",
		"description" => "A prescription for a medication",
		"model" => Prescription::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"refillsLeft" => [
				"type" => Type::string(),
				"description" => "The number of refills left",
				"alias" => "refills_left",
			],
			"refillsOriginal" => [
				"type" => Type::string(),
				"description" => "The number of refills originally prescribed",
				"alias" => "refills_original",
			],
			"acute" => [
				"type" => Type::boolean(),
				"description" => "Is the prescription for an acute event",
			],
			"user" => (new UserField())->toArray(),
			"patient" => (new PatientField([
				"description" => "The patient associated with the prescription",
			]))->toArray(),
			"item" => (new ItemField([
				"description" => "The item prescribed",
			]))->toArray(),
			"location" => (new LocationField([
				"description" => "The location for this prescription",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" => "The organization for this prescription",
			]))->toArray(),
			"dispensations" => (new DispensationField([
				"isList" => true,
				"description" => "The dispensations of this prescription",
			]))->toArray(),
		];
	}
}
