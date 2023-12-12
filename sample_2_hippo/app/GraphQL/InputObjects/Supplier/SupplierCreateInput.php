<?php

namespace App\GraphQL\InputObjects\Supplier;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\SupplierGraphQLType;
use GraphQL\Type\Definition\Type;

class SupplierCreateInput extends HippoInputType
{
	protected $attributes = [
		"name" => "supplierCreateInput",
		"description" => "A supplier to be created",
	];

	protected $graphQLType = SupplierGraphQLType::class;

	protected $inputObject = true;

	/**
	 * @return array[]
	 * @throws SubdomainNotConfiguredException
	 */
	public function fields(): array
	{
		$subdomainName = $this->connectToSubdomain();

		return [
			"companyName" => [
				"type" => Type::string(),
				"description" => "The company name",
				"alias" => "company_name",
			],
			"accountNumber" => [
				"type" => Type::string(),
				"description" => "The practice account number for the supplier",
				"alias" => "account_number",
			],
			"contactName" => [
				"type" => Type::string(),
				"description" =>
					"The name of the person designated as the point of contact for a supplier",
				"rules" => ["max:191"],
				"alias" => "contact_name",
			],
			"emailAddress" => [
				"type" => Type::string(),
				"description" => "The contact's email address",
				"rules" => ["max:191"],
				"alias" => "email_address",
			],
			"phoneNumber" => [
				"type" => Type::string(),
				"description" => "The supplier's phone number",
				"rules" => ["max:191"],
				"alias" => "phone_number",
			],
			"address1" => [
				"type" => Type::string(),
				"description" => "The first line of the address",
				"rules" => ["max:191"],
				"alias" => "address_1",
			],
			"address2" => [
				"type" => Type::string(),
				"description" => "The second line of the address",
				"rules" => ["max:191"],
				"alias" => "address_2",
			],
			"city" => [
				"type" => Type::string(),
				"description" => "The city the supplier is located in",
				"rules" => ["max:191"],
			],
			"zipCode" => [
				"type" => Type::string(),
				"description" =>
					"The zip code associated with the address of the supplier",
				"rules" => ["max:191"],
				"alias" => "zip_code",
			],
			"state" => [
				"type" => Type::int(),
				"description" => "Subregion state code",
				"relation" => true,
				"default" => null,
				"alias" => "state_id",
			],
		];
	}
}
