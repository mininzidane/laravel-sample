<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SupplierGraphQLType;

class SupplierField extends HippoField
{
	protected $graphQLType = SupplierGraphQLType::class;
	protected $permissionName = "GraphQL: View Suppliers";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Suppliers",
	];
}
