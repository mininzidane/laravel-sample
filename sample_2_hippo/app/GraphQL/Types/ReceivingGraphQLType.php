<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ReceivingStatusField;
use App\GraphQL\Fields\ItemField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\ReceivingItemField;
use App\GraphQL\Fields\SupplierField;
use App\GraphQL\Fields\UserField;
use App\Models\Receiving;
use GraphQL\Type\Definition\Type;

class ReceivingGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "receiving";

	protected $attributes = [
		"name" => "Receiving",
		"description" => "Inventory Receiving",
		"model" => Receiving::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the receiving",
			],
			"active" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the invoice is the most recently active invoice for a given patient",
			],
			"receivedAt" => [
				"type" => Type::string(),
				"description" =>
					"The time the shipment was received from the supplier",
				"alias" => "received_at",
			],
			"comment" => [
				"type" => Type::string(),
				"description" => "Any comments associated with the receiving",
			],
			"receivingStatus" => (new ReceivingStatusField([
				"description" => "The status for this receiving",
			]))->toArray(),
			"location" => (new LocationField([
				"description" =>
					"Which location this receiving was received at",
			]))->toArray(),
			"supplier" => (new SupplierField([
				"description" => "Which supplier provided this receiving",
			]))->toArray(),
			"user" => (new UserField([
				"description" => "The user that handled this receiving",
			]))->toArray(),
			"receivingItems" => (new ReceivingItemField([
				"isList" => true,
				"description" => "The items received as part of this receiving",
			]))->toArray(),
			"items" => (new ItemField([
				"isList" => true,
				"description" => "The items themselves that were received",
			]))->toArray(),
		];
	}
}
