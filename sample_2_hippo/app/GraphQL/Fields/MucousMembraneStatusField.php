<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\MucousMembraneStatusGraphQLType;

class MucousMembraneStatusField extends HippoField
{
	protected $graphQLType = MucousMembraneStatusGraphQLType::class;
	protected $permissionName = "GraphQL: View Mucous Membrane Statuses";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Mucous Membrane Statuses",
	];
}
