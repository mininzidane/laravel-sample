<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\NameArguments;
use App\GraphQL\Arguments\TreatmentArguments;
use App\Models\TreatmentSheetTreatment;

class TreatmentSheetTreatmentQuery extends AppHippoQuery
{
	protected $model = TreatmentSheetTreatment::class;

	protected $permissionName = "Treatment Sheet Treatments: Read";

	protected $attributes = [
		"name" => "treatmentSheetTreatmentQuery",
	];

	protected $arguments = [NameArguments::class, TreatmentArguments::class];
}
