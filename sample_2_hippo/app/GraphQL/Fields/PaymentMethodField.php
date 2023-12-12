<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PaymentMethodGraphQLType;

class PaymentMethodField extends HippoField
{
	protected $graphQLType = PaymentMethodGraphQLType::class;
	protected $permissionName = "GraphQL: View Payment Methods";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Payment Methods",
	];
}
