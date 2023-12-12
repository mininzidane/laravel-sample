<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ClearentTransactionField;
use App\GraphQL\Fields\CreditField;
use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\OwnerField;
use App\GraphQL\Fields\PaymentMethodField;
use App\GraphQL\Fields\PaymentPlatformField;
use App\Models\Payment;
use GraphQL\Type\Definition\Type;

class PaymentGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "payment";

	protected $attributes = [
		"name" => "Payment",
		"description" => "A payment for a sale",
		"model" => Payment::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::string(),
				"description" => "The id of the resource",
			],
			"amount" => [
				"type" => Type::float(),
				"description" =>
					"The payment amount in globally configured currency",
			],
			"receivedAt" => [
				"type" => Type::string(),
				"description" => "The time the payment was received",
				"alias" => "received_at",
				"rules" => ["date"],
			],
			"owner" => (new OwnerField([
				"description" => "The owner who made this payment",
			]))->toArray(),
			"credit" => (new CreditField([
				"description" =>
					"The credit used for this payment if one exists",
			]))->toArray(),
			"paymentMethod" => (new PaymentMethodField([
				"description" => "The payment method used for this payment",
			]))->toArray(),
			"paymentPlatform" => (new PaymentPlatformField([
				"description" => "Which payment platform was used",
			]))->toArray(),
			"clearentTransaction" => (new ClearentTransactionField([
				"description" =>
					"The Clearent transaction associated with this payment",
			]))->toArray(),
			"invoices" => (new InvoiceField([
				"isList" => true,
				"description" => "The invoices associated with this payment",
			]))->toArray(),
		];
	}
}
