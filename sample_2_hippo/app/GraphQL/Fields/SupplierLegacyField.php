<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\SupplierLegacyGraphQLType;

class SupplierLegacyField extends HippoField
{
	protected $graphQLType = SupplierLegacyGraphQLType::class;
	protected $permissionName = "GraphQL: View Legacy Suppliers";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Legacy Suppliers",
	];
}
