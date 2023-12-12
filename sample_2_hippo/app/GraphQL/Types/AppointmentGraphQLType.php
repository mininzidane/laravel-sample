<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentStatusField;
use App\GraphQL\Fields\EventField;
use App\GraphQL\Fields\EventRecurField;
use App\GraphQL\Fields\EventTypeField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\ResourceField;
use App\GraphQL\Fields\TreatmentSheetTreatmentField;
use App\GraphQL\Fields\UserField;
use App\Models\Appointment;
use GraphQL\Type\Definition\Type;

class AppointmentGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "appointment";

	protected $attributes = [
		"name" => "Appointment",
		"description" => "A scheduling appointment",
		"model" => Appointment::class,
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
				"description" => "Appointment name",
			],
			"description" => [
				"type" => Type::string(),
				"description" => "Appointment description",
			],
			"blocked" => [
				"type" => Type::boolean(),
				"description" => "Appointment blocked status",
			],
			"startTime" => [
				"type" => Type::string(),
				"description" => "The starting time of the appointment",
				"alias" => "start_time",
			],
			"duration" => [
				"type" => Type::int(),
				"description" =>
					"How long the appointment is scheduled to last in minutes",
			],
			"user" => (new UserField())->toArray(),
			"creator" => (new UserField())->toArray(),
			"resource" => (new ResourceField([
				"description" => "Resource the appointment occurs with",
			]))->toArray(),
			"patient" => (new PatientField([
				"description" => "The patient associated with the appointment",
			]))->toArray(),
			"event" => (new EventField([
				"description" => "The event associated with the appointment",
			]))->toArray(),
			"recur" => (new EventRecurField([
				"description" => "The recurrence pattern for the appointment",
			]))->toArray(),
			"type" => (new EventTypeField([
				"description" => "The event associated with the appointment",
			]))->toArray(),
			"appointmentStatus" => (new AppointmentStatusField([
				"description" => "The status of the appointment",
			]))->toArray(),
			"treatmentSheetTreatments" => (new TreatmentSheetTreatmentField([
				"isList" => true,
				"description" => "The associated treatment sheet treatments",
			]))->toArray(),
		];
	}
}
