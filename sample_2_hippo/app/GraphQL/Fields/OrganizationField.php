<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\OrganizationGraphQLType;

class OrganizationField extends HippoField
{
	protected $graphQLType = OrganizationGraphQLType::class;
	protected $permissionName = "GraphQL: View Organizations";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Organizations",
	];
}
