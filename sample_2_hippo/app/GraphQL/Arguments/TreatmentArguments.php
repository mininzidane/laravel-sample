<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\TreatmentResolver;
use GraphQL\Type\Definition\Type;

class TreatmentArguments extends AdditionalArguments
{
	public static $resolver = TreatmentResolver::class;

	public function getArguments()
	{
		return [
			"invoice" => [
				"name" => "invoice",
				"type" => Type::int(),
			],
			"appointment" => [
				"name" => "appointment",
				"type" => Type::int(),
			],
			"removed" => [
				"name" => "removed",
				"type" => Type::boolean(),
			],
			"rejected" => [
				"name" => "rejected",
				"type" => Type::boolean(),
			],
			"completed" => [
				"name" => "completed",
				"type" => Type::boolean(),
			],
		];
	}
}
