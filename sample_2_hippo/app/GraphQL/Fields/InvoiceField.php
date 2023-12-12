<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InvoiceGraphQLType;

class InvoiceField extends HippoField
{
	protected $graphQLType = InvoiceGraphQLType::class;
	protected $permissionName = "GraphQL: View Invoices";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Invoices",
	];
}
