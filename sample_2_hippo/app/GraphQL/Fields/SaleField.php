<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SaleGraphQLType;

class SaleField extends HippoField
{
	protected $graphQLType = SaleGraphQLType::class;
	protected $permissionName = "GraphQL: View Sales";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Sales",
	];
}
