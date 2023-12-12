<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\CreditGraphQLType;

class CreditField extends HippoField
{
	protected $graphQLType = CreditGraphQLType::class;
	protected $permissionName = "GraphQL: View Credits";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Credits",
	];
}
