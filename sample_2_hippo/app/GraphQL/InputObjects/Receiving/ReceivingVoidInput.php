<?php

namespace App\GraphQL\InputObjects\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingVoidInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingVoidInput",
		"description" => "The input object for voiding an receiving by id",
	];

	protected $graphQLType = ReceivingGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the Receivings Item",
			],
		];
	}
}
