<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\BreedGraphQLType;

class BreedField extends HippoField
{
	protected $graphQLType = BreedGraphQLType::class;
	protected $permissionName = "GraphQL: View Breeds";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Breeds",
	];
}
