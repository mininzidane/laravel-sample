<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\AppointmentArguments;
use App\Models\Appointment;

class AppointmentQuery extends ApiHippoQuery
{
	protected $model = Appointment::class;

	protected $permissionName = "GraphQL: View Appointments";

	protected $attributes = [
		"name" => "appointmentQuery",
	];

	protected $arguments = [AppointmentArguments::class];
}
