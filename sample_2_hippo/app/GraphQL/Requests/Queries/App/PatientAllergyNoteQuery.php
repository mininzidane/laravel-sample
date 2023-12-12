<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\PatientAllergyNoteArguments;
use App\Models\PatientAllergyNote;

class PatientAllergyNoteQuery extends AppHippoQuery
{
	protected $model = PatientAllergyNote::class;

	protected $permissionName = "Patient Allergy Notes: Read";

	protected $attributes = [
		"name" => "patientAllergyNoteQuery",
	];

	protected $arguments = [PatientAllergyNoteArguments::class];
}
