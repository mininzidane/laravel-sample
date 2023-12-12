<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ItemSpeciesRestriction;

class ItemSpeciesRestrictionsQuery extends ApiHippoQuery
{
	protected $model = ItemSpeciesRestriction::class;

	protected $permissionName = "GraphQL: View Item Species Restrictions";

	protected $attributes = [
		"name" => "itemSpeciesRestrictionQuery",
	];
}
