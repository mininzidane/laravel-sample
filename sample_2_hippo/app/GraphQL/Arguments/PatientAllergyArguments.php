<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\PatientAllergyResolver;
use GraphQL\Type\Definition\Type;

class PatientAllergyArguments extends AdditionalArguments
{
	public static $resolver = PatientAllergyResolver::class;

	public function getArguments()
	{
		return [
			"clientId" => [
				"name" => "clientId",
				"type" => Type::int(),
			],
			"removed" => [
				"name" => "removed",
				"type" => Type::int(),
			],
		];
	}
}
