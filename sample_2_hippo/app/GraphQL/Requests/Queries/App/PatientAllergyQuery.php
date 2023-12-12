<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\PatientAllergyArguments;
use App\Models\PatientAllergy;

class PatientAllergyQuery extends AppHippoQuery
{
	protected $model = PatientAllergy::class;

	protected $permissionName = "Patient Allergies: Read";

	protected $attributes = [
		"name" => "patientAllergyQuery",
	];

	protected $arguments = [PatientAllergyArguments::class];
}
