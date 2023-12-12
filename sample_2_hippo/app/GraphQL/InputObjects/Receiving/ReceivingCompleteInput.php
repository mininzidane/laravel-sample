<?php

namespace App\GraphQL\InputObjects\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingCompleteInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingCompleteInput",
		"description" => "The input object for completing an receiving by id",
	];

	protected $graphQLType = ReceivingGraphQLType::class;

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
