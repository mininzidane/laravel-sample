<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\VaccinationArguments;
use App\Models\Vaccination;

class VaccineQuery extends AppHippoQuery
{
	protected $model = Vaccination::class;

	protected $permissionName = "Patient Vaccines: Read";

	protected $attributes = [
		"name" => "vaccineQuery",
	];

	protected $arguments = [VaccinationArguments::class];
}
