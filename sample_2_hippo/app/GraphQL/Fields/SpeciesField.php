<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SpeciesGraphQLType;

class SpeciesField extends HippoField
{
	protected $graphQLType = SpeciesGraphQLType::class;
	protected $permissionName = "GraphQL: View Species";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Species",
	];
}
