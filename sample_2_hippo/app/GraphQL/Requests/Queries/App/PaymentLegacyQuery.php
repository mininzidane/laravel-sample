<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\PaymentLegacy;

class PaymentLegacyQuery extends AppHippoQuery
{
	protected $model = PaymentLegacy::class;

	protected $permissionName = "Legacy Payments: Read";

	protected $attributes = [
		"name" => "paymentLegacyQuery",
	];
}
