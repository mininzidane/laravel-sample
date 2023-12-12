<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Organization;

class OrganizationQuery extends AppHippoQuery
{
	protected $model = Organization::class;

	protected $permissionName = "Organizations: Read";

	protected $attributes = [
		"name" => "organizationQuery",
	];
}
