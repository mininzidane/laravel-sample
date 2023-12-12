<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\PatientAlertArguments;
use App\Models\PatientAlert;

class PatientAlertQuery extends AppHippoQuery
{
	protected $model = PatientAlert::class;

	protected $permissionName = "Patient Alerts: Read";

	protected $attributes = [
		"name" => "patientQuery",
	];

	protected $arguments = [PatientAlertArguments::class];
}
