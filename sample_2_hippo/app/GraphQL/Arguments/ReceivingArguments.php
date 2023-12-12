<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\ReceivingResolver;
use GraphQL\Type\Definition\Type;

class ReceivingArguments extends AdditionalArguments
{
	public static $resolver = ReceivingResolver::class;

	public function getArguments()
	{
		return [
			"receiving_status" => [
				"name" => "receivingStatus",
				"type" => Type::int(),
			],
			"active" => [
				"name" => "active",
				"type" => Type::boolean(),
			],
			"supplier" => [
				"name" => "supplier",
				"type" => Type::int(),
			],
			"location_id" => [
				"name" => "locationId",
				"type" => Type::int(),
			],
		];
	}
}
