<?php

namespace App\GraphQL\InputObjects\Credit;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\CreditGraphQLType;
use GraphQL\Type\Definition\Type;

class CreditCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "creditCreateInput",
		"description" => "A credit voucher to be updated or created",
	];

	//protected $requiredFields = ['name', 'description'];
	protected $graphQLType = CreditGraphQLType::class;

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
				"description" => "The id of the credit",
				"alias" => "id",
			],
			"number" => [
				"type" => Type::string(),
				"description" => "The unique credit voucher number",
				"alias" => "number",
				"rules" => [],
			],
			"type" => [
				"type" => Type::string(),
				"description" => "Type of card",
				"alias" => "type",
				"rules" => ["required", "in:Account Credit,Gift Card"],
			],
			"currentValue" => [
				"type" => Type::float(),
				"description" => "The current balance of the credit voucher",
				"alias" => "value",
				"rules" => ["gte:0"],
			],
			"originalValue" => [
				"type" => Type::float(),
				"description" =>
					"The value of the credit voucher when first purchased",
				"alias" => "original_value",
				"rules" => ["gte:0"],
			],
			"updatedAt" => [
				"type" => Type::string(),
				"description" => "Date last updated",
				"alias" => "updated_at",
			],
			"owner" => [
				"type" => Type::string(),
				"description" => "Owner id",
				"relation" => true,
				"default" => null,
				"alias" => "owner_id",
				"rules" => ["required_if:input.type,==,Account Credit"],
			],
			"organization" => [
				"type" => Type::listOf(Type::int()),
				"description" => "List of reminders this replaces",
				"rules" => [],
			],
		];
	}
}
