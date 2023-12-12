<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InvoicePaymentGraphQLType;

class InvoicePaymentField extends HippoField
{
	protected $graphQLType = InvoicePaymentGraphQLType::class;
	protected $permissionName = "GraphQL: View Invoice Payments";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Invoice Payment",
	];
}
