<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\PaymentPlatformActivation;

class PaymentPlatformActivationQuery extends AppHippoQuery
{
	protected $model = PaymentPlatformActivation::class;

	protected $permissionName = "Payment Platform Activations: Read";

	protected $attributes = [
		"name" => "paymentPlatformActivationQuery",
	];
}
