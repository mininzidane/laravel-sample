<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\ResourceGraphQLType;

class ResourceField extends HippoField
{
	protected $graphQLType = ResourceGraphQLType::class;
	protected $permissionName = "GraphQL: View Resources";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Resources",
	];
}
