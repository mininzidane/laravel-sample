<?php

namespace App\GraphQL\InputObjects\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingCreateInput",
		"description" => "The input object for creating a new receiving",
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
				"description" => "The id of the receiving",
			],
			"location" => [
				"type" => Type::int(),
				"description" =>
					"The id of the location where this receiving was generated",
				"relation" => true,
				"default" => null,
				"alias" => "location_id",
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Location,id",
				],
			],
			"comment" => [
				"type" => Type::string(),
				"description" => "Any comments associated with the receiving",
			],
		];
	}
}
