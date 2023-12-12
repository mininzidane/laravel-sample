<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\VcpGraphQLType;

class VcpField extends HippoField
{
	protected $graphQLType = VcpGraphQLType::class;
	protected $permissionName = "GraphQL: View Vcp";
	protected $isList = false;

	protected $attributes = [
		//'name' => 'contractId',
		"description" => "Associated Vcp info",
	];
}
