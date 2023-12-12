<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Breed;
use App\GraphQL\Arguments\NameArguments;
use App\GraphQL\Arguments\BreedArguments;

class BreedQuery extends AppHippoQuery
{
	protected $model = Breed::class;

	protected $permissionName = "Breeds: Read";

	protected $attributes = [
		"name" => "breedQuery",
	];

	protected $arguments = [NameArguments::class, BreedArguments::class];
}
