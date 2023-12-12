<?php

namespace App\GraphQL\InputObjects\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\ReceivingGraphQLType;
use GraphQL\Type\Definition\Type;

class ReceivingSaveDetailsInput extends HippoInputType
{
	protected $attributes = [
		"name" => "receivingSaveDetailsInput",
		"description" =>
			"The input object for setting the active receiving by id",
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
			"receiving" => [
				"type" => Type::int(),
				"description" => "The receiving to update",
				"default" => null,
				"rules" => [
					"required",
					"exists:" . $subdomainName . "App\Models\Receiving,id",
				],
			],
			"supplier" => [
				"type" => Type::int(),
				"description" => "The supplier of the receiving",
				"default" => null,
				"rules" => [
					"exists:" . $subdomainName . "App\Models\Supplier,id",
				],
			],
			"comment" => [
				"type" => Type::string(),
				"description" => "The comment associated with this receiving",
			],
		];
	}
}
