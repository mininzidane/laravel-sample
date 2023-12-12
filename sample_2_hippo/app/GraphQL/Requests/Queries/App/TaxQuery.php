<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Tax;

class TaxQuery extends AppHippoQuery
{
	protected $model = Tax::class;

	protected $permissionName = "Taxes: Read";

	protected $attributes = [
		"name" => "TaxQuery",
	];
}
