<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\PatientAllergyNoteResolver;
use GraphQL\Type\Definition\Type;

class PatientAllergyNoteArguments extends AdditionalArguments
{
	public static $resolver = PatientAllergyNoteResolver::class;

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
