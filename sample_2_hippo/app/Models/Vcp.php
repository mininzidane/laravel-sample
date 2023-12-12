<?php

namespace App\Models;

use App\GraphQL\Types\VcpGraphQLType;
use App\Repositories\Vcp\VcpRepository;

class Vcp extends HippoModel
{
	public static $graphQLType = VcpGraphQLType::class;
	protected $table = "tblClients";
	protected $primaryKey = "id";
	protected $appends = ["vcpHealth"];

	public function getVcpHealthAttribute(): string
	{
		$subdomain = $this->getConnectionName();
		$controller = new VcpRepository();
		return $controller->getContractHealth(
			$subdomain,
			$this->getAttribute("id"),
		);
	}
}
