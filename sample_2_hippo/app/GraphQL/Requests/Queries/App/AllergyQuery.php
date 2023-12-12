<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Allergy;

class AllergyQuery extends AppHippoQuery
{
	protected $model = Allergy::class;

	protected $permissionName = "Allergies: Read";

	protected $attributes = [
		"name" => "allergyQuery",
	];
}
