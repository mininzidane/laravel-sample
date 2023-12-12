<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Payment;

class PaymentQuery extends ApiHippoQuery
{
	protected $model = Payment::class;

	protected $permissionName = "GraphQL: View Payments";

	protected $attributes = [
		"name" => "paymentQuery",
	];
}
