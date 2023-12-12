<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InvoiceStatusGraphQLType;

class InvoiceStatusField extends HippoField
{
	protected $graphQLType = InvoiceStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Invoice Statuses";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Invoice Status",
	];
}
