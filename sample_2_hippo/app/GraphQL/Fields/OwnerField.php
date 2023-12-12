<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\OwnerGraphQLType;

class OwnerField extends HippoField
{
	protected $graphQLType = OwnerGraphQLType::class;
	protected $permissionName = "GraphQL: View Owners";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Owners",
	];
}
