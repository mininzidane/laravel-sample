<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\TimezoneGraphQLType;

class TimezoneField extends HippoField
{
	protected $graphQLType = TimezoneGraphQLType::class;
	protected $permissionName = "GraphQL: View Timezones";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Timezones",
	];
}
