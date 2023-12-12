<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ReceivingField;
use App\GraphQL\Fields\StateField;
use App\Models\Supplier;
use GraphQL\Type\Definition\Type;

class SupplierGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "supplier";

	protected $attributes = [
		"name" => "Supplier",
		"description" => "An item supplier",
		"model" => Supplier::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::id()),
				"description" => "The id of the supplier",
			],
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
			"stateId" => [
				"type" => Type::int(),
				"description" =>
					"The zip code associated with the address of the supplier",
				"rules" => ["max:191"],
				"alias" => "state_id",
			],
			"state" => (new StateField([
				"description" => "The state the supplier is located within",
			]))->toArray(),
			"receivings" => (new ReceivingField([
				"isList" => true,
				"description" =>
					"The receivings that have been received from this supplier",
			]))->toArray(),
		];
	}
}
