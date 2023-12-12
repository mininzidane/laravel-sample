<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InvoiceAppliedDiscountGraphQLType;

class InvoiceAppliedDiscountField extends HippoField
{
	protected $graphQLType = InvoiceAppliedDiscountGraphQLType::class;
	protected $permissionName = "GraphQL: View Invoice";
	protected $isList = true;

	protected $attributes = [
		"description" => "Associated applied discounts",
	];
}
