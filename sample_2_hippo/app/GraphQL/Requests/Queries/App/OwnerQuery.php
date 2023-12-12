<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Owner;

class OwnerQuery extends AppHippoQuery
{
	protected $model = Owner::class;

	protected $permissionName = "Owners: Read";

	protected $attributes = [
		"name" => "ownerQuery",
	];
}
