<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\VaccinationGraphQLType;

class VaccinationField extends HippoField
{
	protected $graphQLType = VaccinationGraphQLType::class;
	protected $permissionName = "GraphQL: View Vaccinations";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Vaccinations",
	];
}
