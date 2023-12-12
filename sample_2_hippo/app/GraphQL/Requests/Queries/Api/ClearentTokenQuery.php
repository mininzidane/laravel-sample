<?php

namespace App\GraphQL\Requests\Queries\Api;

use App\Models\ClearentToken;

class ClearentTokenQuery extends ApiHippoQuery
{
	protected $model = ClearentToken::class;

	protected $permissionName = "GraphQL: View Clearent Tokens";

	protected $attributes = [
		"name" => "clearentTokenQuery",
	];
}
