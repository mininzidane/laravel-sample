<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OwnerField;
use App\GraphQL\Fields\SupplierField;
use App\Models\State;
use GraphQL\Type\Definition\Type;

class StateGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "subregion";

	protected $attributes = [
		"name" => "Subregion",
		"description" => "A state in a country",
		"model" => State::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id for the state",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the state",
			],
			"code" => [
				"type" => Type::string(),
				"description" => "The two character code for the state",
			],
			"iso" => [
				"type" => Type::string(),
				"description" => "Two character abbreviation for the country",
			],
			"owners" => (new OwnerField([
				"isList" => true,
				"description" => "The owners residing in this state",
			]))->toArray(),
			"locations" => (new LocationField([
				"isList" => true,
				"description" => "The locations that are in this state",
			]))->toArray(),
			"suppliers" => (new SupplierField([
				"isList" => true,
				"description" => "The suppliers associated with this state",
			]))->toArray(),
		];
	}
}
