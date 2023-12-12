<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PaymentTypeGraphQLType;

class PaymentTypeField extends HippoField
{
	protected $graphQLType = PaymentTypeGraphQLType::class;
	protected $permissionName = "GraphQL: View Payment Types";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Payment Types",
	];
}
