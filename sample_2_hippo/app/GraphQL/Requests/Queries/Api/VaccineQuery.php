<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\VaccinationArguments;
use App\Models\Vaccination;

class VaccineQuery extends ApiHippoQuery
{
	protected $model = Vaccination::class;

	protected $permissionName = "GraphQL: View Timezones";

	protected $attributes = [
		"name" => "vaccineQuery",
	];

	protected $arguments = [VaccinationArguments::class];
}
