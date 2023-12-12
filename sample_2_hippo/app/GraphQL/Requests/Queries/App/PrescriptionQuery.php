<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Prescription;

class PrescriptionQuery extends AppHippoQuery
{
	protected $model = Prescription::class;

	protected $permissionName = "Medication Prescriptions: Read";

	protected $attributes = [
		"name" => "prescriptionQuery",
	];
}
