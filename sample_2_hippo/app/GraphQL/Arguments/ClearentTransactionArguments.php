<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\ClearentTransactionResolver;
use GraphQL\Type\Definition\Type;

class ClearentTransactionArguments extends AdditionalArguments
{
	public static $resolver = ClearentTransactionResolver::class;

	public function getArguments()
	{
		return [
			"clearentTerminalId" => [
				"name" => "clearentTerminalId",
				"type" => Type::int(),
			],
		];
	}
}
