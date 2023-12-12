<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\OrganizationSetting;
use App\GraphQL\Arguments\OrganizationSettingArguments;

class OrganizationSettingQuery extends AppHippoQuery
{
	protected $model = OrganizationSetting::class;

	protected $permissionName = "Organization Settings: Read";

	protected $attributes = [
		"name" => "organizationSettingQuery",
	];

	protected $arguments = [OrganizationSettingArguments::class];
}
