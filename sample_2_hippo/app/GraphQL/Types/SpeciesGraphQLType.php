<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\BreedField;
use App\GraphQL\Fields\GenderField;
use App\GraphQL\Fields\ItemSpeciesRestrictionField;
use App\GraphQL\Fields\PatientField;
use App\Models\Species;
use GraphQL\Type\Definition\Type;

class SpeciesGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "species";

	protected $attributes = [
		"name" => "Species",
		"description" => "A patient species",
		"model" => Species::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the species",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the species",
			],
			"breeds" => (new BreedField([
				"isList" => true,
				"description" => "The breeds available for this species",
			]))->toArray(),
			"genders" => (new GenderField([
				"isList" => true,
				"description" => "The genders available for this species",
			]))->toArray(),
			"patients" => (new PatientField([
				"isList" => true,
				"description" => "The patients associated with this species",
			]))->toArray(),
			"itemSpeciesRestrictions" => (new ItemSpeciesRestrictionField([
				"isList" => true,
			]))->toArray(),
			"relationshipNumber" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The species relations to other tables",
				"alias" => "relationship_number",
			],
			"patients" => (new PatientField(["isList" => true]))->toArray(),
		];
	}
}
