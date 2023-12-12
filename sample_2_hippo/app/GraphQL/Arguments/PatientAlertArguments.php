<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\PatientAlertResolver;
use GraphQL\Type\Definition\Type;

class PatientAlertArguments extends AdditionalArguments
{
	public static $resolver = PatientAlertResolver::class;

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
