<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\InvoiceItemGraphQLType;

class InvoiceItemField extends HippoField
{
	protected $graphQLType = InvoiceItemGraphQLType::class;
	protected $permissionName = "GraphQL: View Invoice Items";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Invoice Item(s)",
	];
}
