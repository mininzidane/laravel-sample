<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\HydrationStatus;
use App\GraphQL\Arguments\NameArguments;

class HydrationStatusQuery extends AppHippoQuery
{
	protected $model = HydrationStatus::class;

	protected $permissionName = "Hydration Statuses: Read";

	protected $attributes = [
		"name" => "hydrationStatusQuery",
	];

	protected $arguments = [NameArguments::class];
}
