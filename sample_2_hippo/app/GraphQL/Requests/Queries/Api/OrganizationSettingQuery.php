<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\GraphQL\Requests\Queries\App\AppHippoQuery;
use App\Models\OrganizationSetting;

class OrganizationSettingQuery extends AppHippoQuery
{
	protected $model = OrganizationSetting::class;

	protected $permissionName = "GraphQL: View Organizations";

	protected $attributes = [
		"name" => "organizationSettingQuery",
	];
}
