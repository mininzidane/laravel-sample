<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SaleStatusGraphQLType;

class SaleStatusField extends HippoField
{
	protected $graphQLType = SaleStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Sale Statuses";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Sale Statuses",
	];
}
