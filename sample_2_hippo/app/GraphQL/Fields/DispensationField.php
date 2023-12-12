<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\DispensationGraphQLType;

class DispensationField extends HippoField
{
	protected $graphQLType = DispensationGraphQLType::class;
	protected $permissionName = "GraphQL: View Dispensations";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Dispensations",
	];
}
