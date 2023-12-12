<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\Models\AppointmentStatus;
use GraphQL\Type\Definition\Type;

class AppointmentStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "appointmentStatus";

	protected $attributes = [
		"name" => "AppointmentStatus",
		"description" => "A scheduling appointment status",
		"model" => AppointmentStatus::class,
	];

	public function columns(): array
	{
		return [
			"name" => [
				"type" => Type::string(),
				"description" => "The status key",
				"alias" => "status_key",
			],
			"pretty" => [
				"type" => Type::string(),
				"description" => "Appointment name",
				"alias" => "status_name",
			],
			"inHospital" => [
				"type" => Type::boolean(),
				"description" =>
					"Does the status mean the patient is in the hospital",
				"alias" => "in_hospital_status",
			],
			"lastVisit" => [
				"type" => Type::boolean(),
				"description" => "Last Visit",
				"alias" => "last_visit_status",
			],
			"defaultStatus" => [
				"type" => Type::boolean(),
				"description" => "Is the status the default applied status",
				"alias" => "default_status",
			],
			"hidden" => [
				"type" => Type::boolean(),
				"description" => "Is the status selectable",
			],
			"appointments" => (new AppointmentField([
				"isList" => true,
			]))->toArray(),
		];
	}
}
