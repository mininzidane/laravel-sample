<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\PatientResolver;
use GraphQL\Type\Definition\Type;

class PatientArguments extends AdditionalArguments
{
	public static $resolver = PatientResolver::class;

	public function getArguments()
	{
		return [
			"patient_id" => [
				"name" => "patientId",
				"type" => Type::id(),
			],
			"patientName" => [
				"name" => "patientName",
				"type" => Type::string(),
			],
			"ownerName" => [
				"name" => "ownerName",
				"type" => Type::string(),
			],
			"ownerId" => [
				"name" => "ownerId",
				"type" => Type::id(),
			],
			"licenseId" => [
				"name" => "licenseId",
				"type" => Type::string(),
			],
			"microchipId" => [
				"name" => "microchipId",
				"type" => Type::string(),
			],
			"email" => [
				"name" => "email",
				"type" => Type::string(),
			],
			"phone" => [
				"name" => "phone",
				"type" => Type::string(),
			],
			"serialNumber" => [
				"name" => "serialNumber",
				"type" => Type::string(),
			],
			"deceased" => [
				"name" => "deceased",
				"type" => Type::boolean(),
			],
			"smartSearch" => [
				"name" => "smartSearch",
				"type" => Type::string(),
			],
		];
	}
}
