<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\DrugAllergy;

class DrugAllergyQuery extends AppHippoQuery
{
	protected $model = DrugAllergy::class;

	protected $permissionName = "Drug Allergies: Read";

	protected $attributes = [
		"name" => "drugAllergyQuery",
	];
}
