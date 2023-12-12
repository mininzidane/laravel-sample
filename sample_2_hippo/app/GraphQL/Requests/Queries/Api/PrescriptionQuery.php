<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Prescription;

class PrescriptionQuery extends ApiHippoQuery
{
	protected $model = Prescription::class;

	protected $permissionName = "GraphQL: View Prescriptions";

	protected $attributes = [
		"name" => "prescriptionQuery",
	];
}
