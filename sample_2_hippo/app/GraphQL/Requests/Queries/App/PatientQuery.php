<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\PatientArguments;
use App\Models\Patient;

class PatientQuery extends AppHippoQuery
{
	protected $model = Patient::class;

	protected $permissionName = "Patients: Read";

	protected $attributes = [
		"name" => "patientQuery",
	];

	protected $arguments = [PatientArguments::class];
}
