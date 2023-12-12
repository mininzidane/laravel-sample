<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ClearentTokenGraphQLType;

class ClearentTokenField extends HippoField
{
	protected $graphQLType = ClearentTokenGraphQLType::class;
	protected $permissionName = "GraphQL: View Clearent Tokens";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Clearent Tokens",
	];
}
