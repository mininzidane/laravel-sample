<?php

namespace App\GraphQL\InputObjects\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingSetActiveInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingSetActiveInput",
		"description" =>
			"The input object for setting the active receiving by id",
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
			"receivingId" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the receiving to set active",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Receiving,id",
				],
			],
		];
	}
}
