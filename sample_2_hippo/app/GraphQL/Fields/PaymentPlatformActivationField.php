<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\PaymentPlatformActivationGraphQLType;

class PaymentPlatformActivationField extends HippoField
{
	protected $graphQLType = PaymentPlatformActivationGraphQLType::class;
	protected $permissionName = "GraphQL: View Payment Platform Activations";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated payment platform activations",
	];
}
