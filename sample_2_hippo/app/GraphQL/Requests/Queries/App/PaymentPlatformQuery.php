<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\PaymentPlatform;

class PaymentPlatformQuery extends AppHippoQuery
{
	protected $model = PaymentPlatform::class;

	protected $permissionName = "Payment Platforms: Read";

	protected $attributes = [
		"name" => "paymentPlatformQuery",
	];
}
