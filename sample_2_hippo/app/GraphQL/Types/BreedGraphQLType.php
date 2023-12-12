<?php

namespace App\GraphQL\Types;

use App\Models\Breed;
use App\GraphQL\Fields\SpeciesField;
use GraphQL\Type\Definition\Type;

class BreedGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "breed";

	protected $attributes = [
		"name" => "Breed",
		"description" => "A patient breed",
		"model" => Breed::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the breed",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the breed",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "The species of the breed",
			],
			"patientCount" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The breed relations to patients",
				"alias" => "patient_count",
			],
		];
	}
}
