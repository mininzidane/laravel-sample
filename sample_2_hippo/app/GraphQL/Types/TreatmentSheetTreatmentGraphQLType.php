<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\UserField;
use App\Models\TreatmentSheetTreatment;
use GraphQL\Type\Definition\Type;

class TreatmentSheetTreatmentGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "treatmentSheetTreatment";

	protected $attributes = [
		"name" => "TreatmentSheetTreatment",
		"description" =>
			"Details for a given inventory treatment sheet treatment",
		"model" => TreatmentSheetTreatment::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::id()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the treatment provided",
				"alias" => "treatment_name",
			],
			"line" => [
				"type" => Type::int(),
				"description" => "The line of the treatment provided",
			],
			"quantity" => [
				"type" => Type::float(),
				"description" =>
					"The quantity of the item provided as part of the treatment",
				"alias" => "qty",
			],
			"due" => [
				"type" => Type::string(),
				"description" =>
					"The date when the treatment is to be administered",
			],
			"removedReason" => [
				"type" => Type::string(),
				"description" => "The reason why the treatment was removed",
				"alias" => "removed_reason",
			],
			"rejectedReason" => [
				"type" => Type::string(),
				"description" => "The reason why the treatment was rejected",
				"alias" => "rejected_reason",
			],
			"rejected" => [
				"type" => Type::boolean(),
				"description" => "Whether or not the treatment was rejected",
			],
			"completed" => [
				"type" => Type::boolean(),
				"description" => "Whether or not the treatment is completed",
			],
			"completedTime" => [
				"type" => Type::string(),
				"description" => "The date the treatment was completed",
				"alias" => "completed_time",
			],
			"chartNote" => [
				"type" => Type::string(),
				"description" => "Any notes about this treatment",
				"alias" => "chart_note",
			],
			"recur" => [
				"type" => Type::string(),
				"description" => "The recurrence for this treatment",
			],
			"recurNextDue" => [
				"type" => Type::string(),
				"description" =>
					"The date when the next recurrence of this treatment is due",
				"alias" => "recur_next_due",
			],
			"removed" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not this treatment has been removed",
			],
			"appointment" => new AppointmentField([
				"description" =>
					"The appointment associated with this treatment",
			]),
			"patient" => new PatientField([
				"description" => "The patient associated with this treatment",
			]),
			"item" => new ItemField([
				"description" => "The item associated with this treatment",
			]),
			"invoice" => new InvoiceField([
				"description" => "The invoice associated with this treatment",
			]),
			"user" => new UserField([
				"description" => "The user associated with this treatment",
			]),
		];
	}
}
