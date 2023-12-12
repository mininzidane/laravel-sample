<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ItemArguments;
use App\GraphQL\Arguments\NameArguments;
use App\GraphQL\Arguments\TreatmentArguments;
use App\Models\Item;
use App\Models\TreatmentSheetTreatment;

class TreatmentSheetTreatmentQuery extends ApiHippoQuery
{
	protected $model = TreatmentSheetTreatment::class;

	protected $permissionName = "GraphQL: View Treatment Sheet Treatments";

	protected $attributes = [
		"name" => "treatmentSheetTreatmentQuery",
	];

	protected $arguments = [NameArguments::class, TreatmentArguments::class];
}
