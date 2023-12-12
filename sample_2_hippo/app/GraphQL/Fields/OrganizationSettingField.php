<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\OrganizationSettingGraphQLType;

class OrganizationSettingField extends HippoField
{
	protected $graphQLType = OrganizationSettingGraphQLType::class;
	protected $permissionName = "GraphQL: View Vaccinations";
	protected $isList = true;

	protected $attributes = [
		"description" => "Associated Organization Setting",
	];
}
