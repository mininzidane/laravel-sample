<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PatientGraphQLType;

class PatientField extends HippoField
{
	protected $graphQLType = PatientGraphQLType::class;
	protected $permissionName = "GraphQL: View Patients";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Patients",
	];
}
