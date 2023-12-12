<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PatientAllergyGraphQLType;

class PatientAllergyField extends HippoField
{
	protected $graphQLType = PatientAllergyGraphQLType::class;
	protected $permissionName = "GraphQL: View Allergies";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Allergy",
	];
}
