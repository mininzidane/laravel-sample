<?php

namespace App\GraphQL\InputObjects\Tax;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\TaxGraphQLType;
use GraphQL\Type\Definition\Type;

class TaxCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "taxCreateInput",
		"description" => "Tax for CRUD operations",
	];

	protected $requiredFields = ["name", "percent"];

	protected $graphQLType = TaxGraphQLType::class;

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
				"description" => "The id of the resource",
			],
			"name" => [
				"name" => "name",
				"type" => Type::string(),
				"description" => "The name of the tax",
			],
			"percent" => [
				"name" => "percent",
				"type" => Type::float(),
				"description" => "Tax percentage",
			],
		];
	}
}
