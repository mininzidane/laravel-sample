<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\StatusNameArguments;
use App\Models\AppointmentStatus;

class AppointmentStatusQuery extends AppHippoQuery
{
	protected $model = AppointmentStatus::class;

	protected $permissionName = "Appointment Statuses: Read";

	protected $attributes = [
		"name" => "appointmentStatusQuery",
	];

	protected $arguments = [StatusNameArguments::class];
}
