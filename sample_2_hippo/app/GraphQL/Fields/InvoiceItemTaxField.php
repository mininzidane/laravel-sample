<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InvoiceItemTaxGraphQLType;

class InvoiceItemTaxField extends HippoField
{
	protected $graphQLType = InvoiceItemTaxGraphQLType::class;
	protected $permissionName = "GraphQL: View Invoice Item Taxes";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Invoice Item Taxes",
	];
}
