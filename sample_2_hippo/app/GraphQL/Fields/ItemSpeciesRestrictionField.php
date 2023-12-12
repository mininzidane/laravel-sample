<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ItemSpeciesRestrictionGraphQLType;

class ItemSpeciesRestrictionField extends HippoField
{
	protected $graphQLType = ItemSpeciesRestrictionGraphQLType::class;
	protected $permissionName = "GraphQL: View Item Species Restrictions";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Item Species Restrictions",
	];
}
