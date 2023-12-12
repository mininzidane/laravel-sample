<?php

namespace App\Models;

use App\GraphQL\Types\AccessLevelGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessLevel extends HippoModel
{
	use hasFactory;

	public static $graphQLType = AccessLevelGraphQLType::class;

	protected $table = "roles";
}
