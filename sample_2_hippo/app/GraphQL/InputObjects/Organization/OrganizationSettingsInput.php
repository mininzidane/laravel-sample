<?php

namespace App\GraphQL\InputObjects\Organization;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\OrganizationSettingGraphQLType;
use GraphQL\Type\Definition\Type;

class OrganizationSettingsInput extends HippoInputType
{
	protected $attributes = [
		"name" => "organizationSettingsInput",
		"description" => "Organization Settings for CRUD operations",
	];

	protected $graphQLType = OrganizationSettingGraphQLType::class;

	public function fields(): array
	{
		return [
			"setting_name" => [
				"name" => "setting_name",
				"type" => Type::nonNull(Type::string()),
			],
			"setting_value" => [
				"name" => "setting_value",
				"type" => Type::string(),
			],
		];
	}
}
