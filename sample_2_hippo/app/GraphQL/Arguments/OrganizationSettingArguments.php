<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\OrganizationSettingResolver;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;

class OrganizationSettingArguments extends AdditionalArguments
{
	public static $resolver = OrganizationSettingResolver::class;

	public function getArguments()
	{
		return [
			"settingName" => [
				"name" => "setting_name",
				"type" => Type::string(),
				"rules" => ["string"],
			],
		];
	}
}
