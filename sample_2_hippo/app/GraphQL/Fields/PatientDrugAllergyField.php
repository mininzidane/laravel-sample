<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PatientDrugAllergyGraphQLType;

class PatientDrugAllergyField extends HippoField
{
	protected $graphQLType = PatientDrugAllergyGraphQLType::class;
	protected $permissionName = "GraphQL: View Allergies";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Drug Allergies",
	];
}
