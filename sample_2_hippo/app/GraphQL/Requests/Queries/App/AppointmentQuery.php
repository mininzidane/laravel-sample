<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\AppointmentArguments;
use App\Models\Appointment;

class AppointmentQuery extends AppHippoQuery
{
	protected $model = Appointment::class;

	protected $permissionName = "Appointments: Read";

	protected $attributes = [
		"name" => "appointmentQuery",
	];

	protected $arguments = [AppointmentArguments::class];
}
