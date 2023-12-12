<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\Models\ReminderInterval;
use GraphQL\Type\Definition\Type;

class ReminderIntervalGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "reminderInterval";

	protected $attributes = [
		"name" => "ReminderInterval",
		"description" => "An available reminder interval for associated items",
		"model" => ReminderInterval::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"code" => [
				"type" => Type::string(),
				"description" => "The shorthand code for an interval",
				"rules" => ["max:10"],
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The descriptive name of the time interval",
				"rules" => ["max:191"],
			],
			"items" => (new ItemField([
				"isList" => true,
				"description" =>
					"The items associated with this reminder interval",
			]))->toArray(),
			"invoiceItems" => (new InvoiceItemField([
				"isList" => true,
				"description" =>
					"Items that are part of an invoice that are associated with this reminder interval",
			]))->toArray(),
		];
	}
}
