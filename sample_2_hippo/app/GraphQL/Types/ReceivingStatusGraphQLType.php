<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ReceivingField;
use App\Models\ReceivingStatus;
use GraphQL\Type\Definition\Type;

class ReceivingStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "receivingStatus";

	protected $attributes = [
		"name" => "ReceivingStatus",
		"description" => "Possible statuses for receivings",
		"model" => ReceivingStatus::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"name" => [
				"type" => Type::string(),
				"description" =>
					"The human-readable name of the payment method",
				"rules" => ["max:191"],
			],
			"receivings" => (new ReceivingField([
				"isList" => true,
				"description" => "Receivings with this receiving status",
			]))->toArray(),
		];
	}
}
