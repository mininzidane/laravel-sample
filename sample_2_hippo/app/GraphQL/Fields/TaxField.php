<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\TaxGraphQLType;

class TaxField extends HippoField
{
	protected $graphQLType = TaxGraphQLType::class;
	protected $permissionName = "GraphQL: View Taxes";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Taxes",
	];
}
