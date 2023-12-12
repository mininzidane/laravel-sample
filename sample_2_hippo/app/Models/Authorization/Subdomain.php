<?php

namespace App\Models\Authorization;

use App\GraphQL\Types\SubdomainGraphQLType;
use App\Models\HippoModel;

class Subdomain extends HippoModel
{
	protected $table = "subdomains";

	public static $graphQLType = SubdomainGraphQLType::class;

	public function permission()
	{
		return $this->belongsTo(Permission::class);
	}
}
