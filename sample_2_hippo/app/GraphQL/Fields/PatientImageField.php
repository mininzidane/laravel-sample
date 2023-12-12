<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PatientImageGraphQLType;

class PatientImageField extends HippoField
{
	protected $graphQLType = PatientImageGraphQLType::class;
	protected $permissionName = "GraphQL: View Patients";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Patient Images",
	];
}
