<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\InvoiceItemResolver;
use GraphQL\Type\Definition\Type;

class InvoiceItemArguments extends AdditionalArguments
{
	public static $resolver = InvoiceItemResolver::class;

	public function getArguments()
	{
		return [
			"categoryId" => [
				"name" => "categoryId",
				"type" => Type::id(),
			],
			"typeId" => [
				"name" => "typeId",
				"type" => Type::id(),
			],
			"name" => [
				"name" => "name",
				"type" => Type::string(),
			],
			"ownerId" => [
				"name" => "ownerId",
				"type" => Type::id(),
			],
			"locationId" => [
				"name" => "locationId",
				"type" => Type::id(),
			],
			"invoiceStatus" => [
				"name" => "invoiceStatus",
				"type" => Type::int(),
			],
		];
	}
}
