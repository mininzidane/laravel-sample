<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\UserField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PatientField;
use App\Models\PatientAlert;
use GraphQL\Type\Definition\Type;

class PatientAlertGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "patientAlerts";

	protected $attributes = [
		"name" => "PatientAlert",
		"description" => "Alerts for patients",
		"model" => PatientAlert::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the alert",
				"alias" => "id",
			],
			"clientId" => [
				"type" => Type::int(),
				"description" => "The id of the alert",
				"alias" => "client_id",
			],

			"patient" => (new PatientField([
				"description" => "The patient associated with the appointment",
			]))->toArray(),

			"organization" => (new OrganizationField([
				"description" => "The associated organization",
			]))->toArray(),

			"addedBy" => (new UserField([
				"description" => "The user that created the sale",
				"aliases" => "added_by",
			]))->toArray(),

			"description" => [
				"type" => Type::string(),
				"description" => "Description of alert",
			],

			"current" => [
				"type" => Type::boolean(),
				"description" => "Whether the alert is active",
			],

			"removed" => [
				"type" => Type::boolean(),
				"description" => "Has the alert been removed",
			],

			"updatedAt" => [
				"type" => Type::string(),
				"description" => "Date last updated",
				"alias" => "updated_at",
			],
		];
	}
}
