<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Dispensation;

class DispensationQuery extends AppHippoQuery
{
	protected $model = Dispensation::class;

	protected $permissionName = "Medication Dispensations: Read";

	protected $attributes = [
		"name" => "dispensationQuery",
	];
}
