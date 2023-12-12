<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\VaccinationResolver;
use GraphQL\Type\Definition\Type;

class VaccinationArguments extends AdditionalArguments
{
	public static $resolver = VaccinationResolver::class;

	public function getArguments()
	{
		return [
			"patient" => [
				"name" => "patient",
				"type" => Type::id(),
			],
			"item" => [
				"name" => "item",
				"type" => Type::id(),
			],
		];
	}
}
