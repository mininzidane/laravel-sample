<?php

namespace App\GraphQL\Types;

use App\Models\Vcp;
use GraphQL\Type\Definition\Type;

class VcpGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "vcp";

	protected $attributes = [
		"name" => "Vcp",
		"description" => "Vcp information",
		"model" => Vcp::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "Id for the vaccine",
			],
			"vcpHealth" => [
				"type" => Type::string(),
				"description" => "The health of the VCP account",
				"selectable" => false,
				"alias" => "vcp_health",
			],
			//            'vcpHealthPdf' => [
			//                'type' => Type::string(),
			//                'description' => 'The health of the VCP account',
			//                'selectable' => false,
			//            ],
		];
	}
}
