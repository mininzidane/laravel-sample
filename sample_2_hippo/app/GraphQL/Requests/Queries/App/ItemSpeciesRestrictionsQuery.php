<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ItemSpeciesRestriction;

class ItemSpeciesRestrictionsQuery extends AppHippoQuery
{
	protected $model = ItemSpeciesRestriction::class;

	protected $permissionName = "Item Species Restrictions: Read";

	protected $attributes = [
		"name" => "itemSpeciesRestrictionQuery",
	];
}
