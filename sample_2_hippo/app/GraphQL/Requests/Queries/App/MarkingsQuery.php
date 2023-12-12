<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\MarkingArguments;
use App\Models\Markings;
use App\GraphQL\Arguments\NameArguments;

class MarkingsQuery extends AppHippoQuery
{
	protected $model = Markings::class;

	protected $permissionName = "Markings: Read";

	protected $attributes = [
		"name" => "markingsQuery",
	];

	protected $arguments = [NameArguments::class, MarkingArguments::class];
}
