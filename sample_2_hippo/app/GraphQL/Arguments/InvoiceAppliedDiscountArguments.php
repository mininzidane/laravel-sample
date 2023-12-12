<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\InvoiceAppliedDiscountResolver;
use GraphQL\Type\Definition\Type;

class InvoiceAppliedDiscountArguments extends AdditionalArguments
{
	public static $resolver = InvoiceAppliedDiscountResolver::class;

	public function getArguments()
	{
		return [
			"discountInvoiceItemId" => [
				"name" => "discountInvoiceItemId",
				"type" => Type::int(),
			],
			"adjustedInvoiceItemId" => [
				"name" => "invoiceIds",
				"type" => Type::listOf(Type::int()),
			],
		];
	}
}
