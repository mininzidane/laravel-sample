<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\LineItemGraphQLType;

class LineItemField extends HippoField
{
	protected $graphQLType = LineItemGraphQLType::class;
	protected $permissionName = "GraphQL: View Line Items";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Line Items",
	];
}
