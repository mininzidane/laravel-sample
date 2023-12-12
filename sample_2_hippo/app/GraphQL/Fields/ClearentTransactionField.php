<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ClearentTransactionGraphQLType;

class ClearentTransactionField extends HippoField
{
	protected $graphQLType = ClearentTransactionGraphQLType::class;
	protected $permissionName = "GraphQL: View Clearent Transactions";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Clearent Transactions",
	];
}
