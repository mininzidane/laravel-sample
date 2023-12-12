<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\PaymentType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class PaymentTypeQuery extends AppHippoQuery
{
	protected $model = PaymentType::class;

	protected $permissionName = "Payment Types: Read";

	protected $attributes = [
		"name" => "paymentTypeQuery",
	];

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$subdomainName = $args["subdomain"];

		$this->connectToSubdomain($subdomainName);

		return $this->model
			::on($subdomainName)
			->distinct()
			->get();
	}
}
