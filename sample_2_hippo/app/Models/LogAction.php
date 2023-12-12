<?php

namespace App\Models;

use App\GraphQL\Types\LogActionGraphQLType;

class LogAction extends HippoModel
{
	public static $graphQLType = LogActionGraphQLType::class;

	protected $table = "tblLogActions";

	protected $fillable = ["action"];
}
