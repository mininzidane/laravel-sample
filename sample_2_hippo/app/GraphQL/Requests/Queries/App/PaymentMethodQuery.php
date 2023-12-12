<?php

namespace App\GraphQL\Requests\Queries\App;

use App\GraphQL\Arguments\ActiveArguments;
use App\Models\PaymentMethod;

class PaymentMethodQuery extends AppHippoQuery
{
	protected $model = PaymentMethod::class;

	protected $permissionName = "Payment Methods: Read";

	protected $attributes = [
		"name" => "paymentMethodQuery",
	];

	protected $arguments = [ActiveArguments::class];
}
