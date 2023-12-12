<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ClearentTransactionField;
use App\GraphQL\Fields\OwnerField;
use App\Models\ClearentToken;
use GraphQL\Type\Definition\Type;

class ClearentTokenGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "clearentToken";

	protected $attributes = [
		"name" => "ClearentToken",
		"description" => "A saved Clearent token",
		"model" => ClearentToken::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
				"alias" => "id",
			],
			"cardToken" => [
				"type" => Type::string(),
				"description" => "The token used to process this saved card.",
				"alias" => "card_token",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of this token",
				"alias" => "name",
			],
			"cardType" => [
				"type" => Type::string(),
				"description" =>
					"The card type of the card used to generate this token",
				"rules" => ["min:1", "max:20"],
				"alias" => "card_type",
			],
			"lastFourDigits" => [
				"type" => Type::string(),
				"description" =>
					"The last four digits of the card used to generate this token",
				"rules" => ["size:4"],
				"alias" => "last_four_digits",
			],
			"expirationDate" => [
				"type" => Type::string(),
				"description" =>
					"The expiration date of the card used to generate this token",
				"rules" => ["size:4"],
				"alias" => "expiration_date",
			],
			"owner" => (new OwnerField([
				"description" => "The owner who this token can be used for",
			]))->toArray(),
			"clearentTransaction" => (new ClearentTransactionField([
				"description" => "The transaction associated with this token",
			]))->toArray(),
		];
	}
}
