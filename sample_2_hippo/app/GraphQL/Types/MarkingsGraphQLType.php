<?php

namespace App\GraphQL\Types;

use App\Models\Markings;
use GraphQL\Type\Definition\Type;

class MarkingsGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "markings";

	protected $attributes = [
		"name" => "Markings",
		"description" => "A patient's markings",
		"model" => Markings::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the marking",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "The species of the marking",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the breed",
			],
			"patientCount" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The markings relations to patients",
				"alias" => "patient_count",
			],
		];
	}
}
