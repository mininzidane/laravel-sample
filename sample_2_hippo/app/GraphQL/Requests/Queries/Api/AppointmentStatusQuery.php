<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\StatusNameArguments;
use App\Models\AppointmentStatus;

class AppointmentStatusQuery extends ApiHippoQuery
{
	protected $model = AppointmentStatus::class;

	protected $permissionName = "GraphQL: View Appointment Statuses";

	protected $attributes = [
		"name" => "appointmentStatusQuery",
	];

	protected $arguments = [StatusNameArguments::class];
}
