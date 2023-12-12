<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\PaymentPlatform;

class PaymentPlatformQuery extends ApiHippoQuery
{
	protected $model = PaymentPlatform::class;

	protected $permissionName = "GraphQL: View Payment Platforms";

	protected $attributes = [
		"name" => "paymentPlatformQuery",
	];
}
