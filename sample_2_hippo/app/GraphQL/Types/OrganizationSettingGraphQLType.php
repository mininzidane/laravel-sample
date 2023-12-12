<?php

namespace App\GraphQL\Types;

use App\Models\OrganizationSetting;
use GraphQL\Type\Definition\Type;

class OrganizationSettingGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "organizationSetting";

	protected $attributes = [
		"name" => "OrganizationSetting",
		"description" => "Organization Settings",
		"model" => OrganizationSetting::class,
	];

	public function columns(): array
	{
		return [
			"settingName" => [
				"type" => Type::string(),
				"description" => "Name of the setting",
				"alias" => "setting_name",
			],
			"settingValue" => [
				"type" => Type::string(),
				"description" => "Value of the setting",
				"alias" => "setting_value",
			],
		];
	}
}
