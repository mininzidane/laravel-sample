<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Arguments\ActiveArguments;
use App\Models\PaymentMethod;

class PaymentMethodQuery extends ApiHippoQuery
{
	protected $model = PaymentMethod::class;

	protected $permissionName = "GraphQL: View Payment Methods";

	protected $attributes = [
		"name" => "paymentMethodQuery",
	];

	protected $arguments = [ActiveArguments::class];
}
