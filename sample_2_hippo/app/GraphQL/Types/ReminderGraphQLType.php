<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\SaleField;
use App\Models\Reminder;
use GraphQL\Type\Definition\Type;

class ReminderGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "reminder";

	protected $attributes = [
		"name" => "Reminder",
		"description" => "An appointment reminder",
		"model" => Reminder::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the reminder",
			],
			"description" => [
				"type" => Type::string(),
				"description" => "A description of the reminder",
			],
			"frequency" => [
				"type" => Type::string(),
				"description" => "How often the reminder occurs",
			],
			"startDate" => [
				"type" => Type::string(),
				"description" => "The date the reminder was created",
				"alias" => "start_date",
			],
			"dueDate" => [
				"type" => Type::string(),
				"description" => "The date the reminder is for",
				"alias" => "due_date",
			],
			"emailSentDate" => [
				"type" => Type::string(),
				"description" => "When the reminder email was sent",
				"alias" => "email_sent",
			],
			"location" => (new LocationField([
				"description" => "Associated Location",
			]))->toArray(),
			"patient" => (new PatientField([
				"description" => "The patient the reminder is for",
			]))->toArray(),
			"item" => (new ItemField([
				"description" => "The item the reminder is for",
			]))->toArray(),
			"sale" => (new SaleField([
				"description" => "The sale the reminder is for",
			]))->toArray(),
			"invoiceItem" => (new InvoiceItemField([
				"description" => "The related invoice item",
			]))->toArray(),
			"invoice" => (new InvoiceField([
				"description" => "The related invoice",
			]))->toArray(),
		];
	}
}
