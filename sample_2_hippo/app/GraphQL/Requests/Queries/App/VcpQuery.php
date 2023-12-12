<?php

namespace App\GraphQL\Requests\Queries\App;

use App\Models\Vcp;

class VcpQuery extends AppHippoQuery
{
	protected $model = Vcp::class;

	protected $permissionName = "VCP: Read";

	protected $attributes = [
		"vcp" => "vcpQuery",
	];
}
