<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Color;
use App\GraphQL\Arguments\NameArguments;
use App\GraphQL\Arguments\ColorArguments;

class ColorQuery extends AppHippoQuery
{
	protected $model = Color::class;

	protected $permissionName = "Color: Read";

	protected $attributes = [
		"name" => "colorQuery",
	];

	protected $arguments = [NameArguments::class, ColorArguments::class];
}
