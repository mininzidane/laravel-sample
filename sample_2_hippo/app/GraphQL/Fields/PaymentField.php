<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PaymentGraphQLType;

class PaymentField extends HippoField
{
	protected $graphQLType = PaymentGraphQLType::class;
	protected $permissionName = "GraphQL: View Payments";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Payments",
	];
}
