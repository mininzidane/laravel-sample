<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\NameArguments;
use App\Models\Breed;

class BreedQuery extends ApiHippoQuery
{
	protected $model = Breed::class;

	protected $permissionName = "GraphQL: View Breeds";

	protected $attributes = [
		"name" => "breedQuery",
	];

	protected $arguments = [NameArguments::class];
}
