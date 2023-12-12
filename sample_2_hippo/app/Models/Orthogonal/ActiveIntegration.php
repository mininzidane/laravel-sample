<?php

namespace App\Models\Orthogonal;

use App\Models\HippoModel;

class ActiveIntegration extends HippoModel
{
	protected $table = "tblActiveIntegrations";

	protected $fillable = [
		"organization_id",
		"location_id",
		"subdomain",
		"integration",
		"vetsource_site_id",
	];
}
