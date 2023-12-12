<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\Organization;

class OrganizationQuery extends ApiHippoQuery
{
	protected $model = Organization::class;

	protected $permissionName = "GraphQL: View Organizations";

	protected $attributes = [
		"name" => "organizationQuery",
	];
}
