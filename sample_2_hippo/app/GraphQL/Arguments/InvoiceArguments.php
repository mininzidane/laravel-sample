<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\InvoiceResolver;
use GraphQL\Type\Definition\Type;

class InvoiceArguments extends AdditionalArguments
{
	public static $resolver = InvoiceResolver::class;

	public function getArguments()
	{
		return [
			"invoice_status" => [
				"name" => "invoiceStatus",
				"type" => Type::int(),
			],
			"active" => [
				"name" => "active",
				"type" => Type::boolean(),
			],
			"ownerName" => [
				"name" => "ownerName",
				"type" => Type::string(),
			],
			"patientName" => [
				"name" => "patientName",
				"type" => Type::string(),
			],
			"ownerId" => [
				"name" => "ownerId",
				"type" => Type::int(),
			],
			"patientId" => [
				"name" => "patientId",
				"type" => Type::int(),
			],
			"locationId" => [
				"name" => "locationId",
				"type" => Type::int(),
			],
			"invoiceIds" => [
				"name" => "invoiceIds",
				"type" => Type::listOf(Type::int()),
			],
			"patientIds" => [
				"name" => "patientIds",
				"type" => Type::listOf(Type::id()),
			],
		];
	}
}
