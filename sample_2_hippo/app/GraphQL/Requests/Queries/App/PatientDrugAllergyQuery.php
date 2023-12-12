<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\PatientDrugAllergyArguments;
use App\Models\PatientDrugAllergy;

class PatientDrugAllergyQuery extends AppHippoQuery
{
	protected $model = PatientDrugAllergy::class;

	protected $permissionName = "Patient Drug Allergies: Read";

	protected $attributes = [
		"name" => "patientDrugAllergyQuery",
	];

	protected $arguments = [PatientDrugAllergyArguments::class];
}
