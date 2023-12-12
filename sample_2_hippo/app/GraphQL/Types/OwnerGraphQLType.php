<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\CreditField;
use App\GraphQL\Fields\InvoiceField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\SaleField;
use App\GraphQL\Fields\StateField;
use App\Models\Owner;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OwnerGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "owner";

	protected $attributes = [
		"name" => "Owner",
		"description" => "A patient owner",
		"model" => Owner::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "",
			],
			"patients" => [
				"type" => Type::listOf(
					GraphQL::type(PatientGraphQLType::getGraphQLTypeName()),
				),
				"description" => "The patient an owner is associated with",
				"resolve" => function ($data, $args) {
					return $data->patients()->get();
				},
			],
			"firstName" => [
				"type" => Type::string(),
				"description" => 'The owner\'s first name',
				"alias" => "first_name",
			],
			"middleName" => [
				"type" => Type::string(),
				"description" => 'The owner\'s middle name',
				"alias" => "middle_name",
			],
			"lastName" => [
				"type" => Type::string(),
				"description" => 'The owner\'s last name',
				"alias" => "last_name",
			],
			"fullName" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => 'The owner\'s full name',
				"alias" => "full_name",
			],
			"address1" => [
				"type" => Type::string(),
				"description" => "Address component 1",
			],
			"address2" => [
				"type" => Type::string(),
				"description" => "Address component 2",
			],
			"city" => [
				"type" => Type::string(),
				"description" => "The city the owner lives in",
			],
			"zip" => [
				"type" => Type::string(),
				"description" => "The zipcode the owner lives in",
			],
			"country" => [
				"type" => Type::string(),
				"description" => "The country the owner lives in",
			],
			"phone" => [
				"type" => Type::string(),
				"description" => 'The owner\'s phone number',
			],
			"email" => [
				"type" => Type::string(),
				"description" => 'The owner\'s email address',
			],
			"notes" => [
				"type" => Type::string(),
				"description" => "User entered details about the owner",
			],
			"primary" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the owner is a primary owner.  Overlaps with the pivot table",
				"resolve" => function ($group) {
					return $group->pivot->primary;
				},
				"selectable" => false,
			],
			"percent" => [
				"type" => Type::string(),
				"description" =>
					"The percent ownership the owner has of the patient",
				"resolve" => function ($group) {
					return $group->pivot->percent;
				},
				"selectable" => false,
			],
			"relationship" => [
				"type" => Type::string(),
				"description" =>
					"The relationship between the owner and patient",
				"resolve" => function ($group) {
					return $group->pivot->relationship_type;
				},
				"selectable" => false,
			],
			"created" => [
				"type" => Type::string(),
				"description" => "When the owner was created",
				"alias" => "timestamp",
			],
			"dateOfBirth" => [
				"type" => Type::string(),
				"description" => "When the owner was born",
				"alias" => "dob",
			],
			"phone2" => [
				"type" => Type::string(),
				"description" => "Alternate phone number for the owner",
				"alias" => "phone_2",
			],
			"phone3" => [
				"type" => Type::string(),
				"description" => "Another alternate phone number for the owner",
				"alias" => "phone_3",
			],
			"driversLicense" => [
				"type" => Type::string(),
				"description" => 'The driver\'s license number for the owner',
				"alias" => "dl_number",
			],
			"preferredCommunication" => [
				"type" => Type::string(),
				"description" =>
					"The method of communication that the owner prefers",
				"alias" => "communication_preference",
			],
			"balance" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The current balance for this owner",
			],
			"referrer" => [
				"type" => Type::string(),
				"description" => "The referrer for this owner",
				"alias" => "refer",
			],
			"accountCreditTotal" => [
				"type" => Type::string(),
				"description" =>
					"The total of the account credits for this owner",
			],
			"sales" => (new SaleField([
				"isList" => true,
				"description" => "Purchases made by this owner",
			]))->toArray(),
			"subregion" => (new StateField([
				"description" => "The state the owner lives in",
				"alias" => "subregion",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" =>
					"The organization that the patient is associated with",
			]))->toArray(),
			"invoices" => new InvoiceField([
				"isList" => true,
				"description" => "Invoices for the patient",
			]),
			"credits" => new CreditField([
				"isList" => true,
				"description" => "The account credits for this owner",
			]),
		];
	}
}
