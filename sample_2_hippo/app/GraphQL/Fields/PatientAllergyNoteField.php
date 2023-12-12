<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PatientAllergyNoteGraphQLType;

class PatientAllergyNoteField extends HippoField
{
	protected $graphQLType = PatientAllergyNoteGraphQLType::class;
	protected $permissionName = "GraphQL: View Allergies";
	protected $primaryKey = "client_id";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Allergy Notes",
	];
}
