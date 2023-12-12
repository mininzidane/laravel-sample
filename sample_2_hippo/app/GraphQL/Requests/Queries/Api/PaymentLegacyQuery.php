<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\PaymentLegacy;

class PaymentLegacyQuery extends ApiHippoQuery
{
	protected $model = PaymentLegacy::class;

	protected $permissionName = "GraphQL: View Legacy Payments";

	protected $attributes = [
		"name" => "paymentLegacyQuery",
	];
}
