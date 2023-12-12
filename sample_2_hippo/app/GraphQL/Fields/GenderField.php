<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\GenderGraphQLType;

class GenderField extends HippoField
{
	protected $graphQLType = GenderGraphQLType::class;
	protected $permissionName = "GraphQL: View Genders";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Genders",
	];
}
