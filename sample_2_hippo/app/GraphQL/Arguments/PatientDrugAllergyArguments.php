<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\PatientDrugAllergyResolver;
use GraphQL\Type\Definition\Type;

class PatientDrugAllergyArguments extends AdditionalArguments
{
	public static $resolver = PatientDrugAllergyResolver::class;

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
