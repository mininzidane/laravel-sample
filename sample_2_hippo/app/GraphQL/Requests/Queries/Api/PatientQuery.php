<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Patient;

class PatientQuery extends ApiHippoQuery
{
	protected $model = Patient::class;

	protected $permissionName = "GraphQL: View Patients";

	protected $attributes = [
		"name" => "patientQuery",
	];
}
