<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\AppointmentGraphQLType;

class AppointmentField extends HippoField
{
	protected $graphQLType = AppointmentGraphQLType::class;
	protected $permissionName = "GraphQL: View Appointments";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Appointments",
	];
}
