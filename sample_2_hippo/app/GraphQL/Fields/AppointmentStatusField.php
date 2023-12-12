<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\AppointmentStatusGraphQLType;

class AppointmentStatusField extends HippoField
{
	protected $graphQLType = AppointmentStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Appointment Statuses";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Appointment Statuses",
	];
}
