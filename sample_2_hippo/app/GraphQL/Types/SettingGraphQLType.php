<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\SettingField;
use App\Models\LocationSetting;
use GraphQL\Type\Definition\Type;

class SettingGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "setting";

	protected $attributes = [
		"name" => "Setting",
		"description" => "A location setting",
		"model" => LocationSetting::class,
	];

	public function columns(): array
	{
		return [
			"location_id" => [
				"type" => Type::int(),
				"description" =>
					"The ID of the location this setting applies to",
			],
			"setting_name" => [
				"type" => Type::string(),
				"description" => "The name of the setting",
			],
			"setting_value" => [
				"type" => Type::string(),
				"description" => "The value of the setting",
			],
		];
	}
}
