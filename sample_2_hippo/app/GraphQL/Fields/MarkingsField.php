<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\MarkingsGraphQLType;

class MarkingsField extends HippoField
{
	protected $graphQLType = MarkingsGraphQLType::class;
	protected $permissionName = "GraphQL: View Markings";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Markings",
	];
}
