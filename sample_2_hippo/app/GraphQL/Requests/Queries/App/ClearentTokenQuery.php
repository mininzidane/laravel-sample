<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\ClearentToken;
use App\GraphQL\Arguments\ClearentTokenArguments;

class ClearentTokenQuery extends AppHippoQuery
{
	protected $model = ClearentToken::class;

	protected $permissionName = "Clearent Tokens: Read";

	protected $attributes = [
		"name" => "clearentTokenQuery",
	];

	protected $arguments = [ClearentTokenArguments::class];
}
