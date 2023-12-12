<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\PaymentPlatformActivation;

class PaymentPlatformActivationQuery extends ApiHippoQuery
{
	protected $model = PaymentPlatformActivation::class;

	protected $permissionName = "GraphQL: View Payment Platform Activations";

	protected $attributes = [
		"name" => "paymentPlatformActivationQuery",
	];
}
