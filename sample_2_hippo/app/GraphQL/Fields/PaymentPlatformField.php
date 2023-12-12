<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PaymentPlatformGraphQLType;

class PaymentPlatformField extends HippoField
{
	protected $graphQLType = PaymentPlatformGraphQLType::class;
	protected $permissionName = "GraphQL: View Payment Platforms";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Payment Platforms",
	];
}
