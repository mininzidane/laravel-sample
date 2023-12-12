<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Payment;

class PaymentQuery extends AppHippoQuery
{
	protected $model = Payment::class;

	protected $permissionName = "Payments: Read";

	protected $attributes = [
		"name" => "paymentQuery",
	];
}
