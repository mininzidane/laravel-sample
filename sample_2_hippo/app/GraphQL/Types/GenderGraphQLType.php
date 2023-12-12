<?php

namespace App\GraphQL\Types;

use App\Models\Gender;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Fields\SpeciesField;

class GenderGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "gender";

	protected $attributes = [
		"name" => "Gender",
		"description" => "A patient gender",
		"model" => Gender::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the gender",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the gender",
				"alias" => "gender",
			],
			"sex" => [
				"type" => Type::string(),
				"description" => "The base gender of the patient",
			],
			"neutered" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether the patient has been spayed or castrated",
			],
			"species" => [
				"type" => Type::string(),
				"description" => "The species of the marking",
			],
			"patientCount" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The gender relations to patients",
				"alias" => "patient_count",
			],
			"speciesType" => (new SpeciesField([
				"description" => "The species this gender exists for",
			]))->toArray(),
		];
	}
}
