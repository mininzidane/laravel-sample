<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\PaymentType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class PaymentTypeQuery extends ApiHippoQuery
{
	protected $model = PaymentType::class;

	protected $permissionName = "GraphQL: View Payment Types";

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
