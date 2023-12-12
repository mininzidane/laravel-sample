<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\ClearentTerminalField;
use App\GraphQL\Fields\DispensationField;
use App\GraphQL\Fields\ItemLegacyField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PaymentPlatformActivationField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\ReminderField;
use App\GraphQL\Fields\ResourceField;
use App\GraphQL\Fields\SaleField;
use App\GraphQL\Fields\StateField;
use App\GraphQL\Fields\TimezoneField;
use App\GraphQL\Fields\UserField;
use App\Models\Location;
use GraphQL\Type\Definition\Type;

class LocationGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "location";

	protected $attributes = [
		"name" => "Location",
		"description" => "A practice location",
		"model" => Location::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the location",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the location",
			],
			"city" => [
				"type" => Type::string(),
				"description" => "The city portion of the address",
			],
			"streetAddress" => [
				"type" => Type::string(),
				"description" => "The street address component of the address",
				"deprecationReason" =>
					"This field is deprecated, use 'address1' instead.",
				"alias" => "address1",
			],
			"address1" => [
				"type" => Type::string(),
				"description" => "The street address component of the address",
				"alias" => "address1",
			],
			"address2" => [
				"type" => Type::string(),
				"description" => "The street address2 component of the address",
			],
			"address3" => [
				"type" => Type::string(),
				"description" => "The street address3 component of the address",
			],
			"zip" => [
				"type" => Type::string(),
				"description" => 'Address\'s regional zip code',
			],
			"country" => [
				"type" => Type::string(),
				"description" => "The nation of the practice location",
			],
			"email" => [
				"type" => Type::string(),
				"description" => "Email address",
			],
			"primary" => [
				"type" => Type::boolean(),
				"description" => "Is the location primary",
				"alias" => "primary",
			],
			"users" => new UserField([
				"isList" => true,
				"description" => "Users associated with this location",
			]),
			"organization" => (new OrganizationField([
				"description" => "The organization the location belongs to",
			]))->toArray(),
			"resources" => (new ResourceField([
				"isList" => true,
				"description" => "Resources available at the location",
			]))->toArray(),
			"items" => (new ItemLegacyField([
				"isList" => true,
				"description" => "The items available at the location",
			]))->toArray(),
			"sales" => (new SaleField([
				"isList" => true,
				"description" => "The sales made at the location",
			]))->toArray(),
			"prescriptions" => (new PrescriptionField([
				"isList" => true,
			]))->toArray(),
			"dispensations" => (new DispensationField([
				"isList" => true,
				"description" =>
					"The dispensations associated with this location",
			]))->toArray(),
			"reminders" => (new ReminderField(["isList" => true]))->toArray(),
			"tz" => (new TimezoneField(["alias" => "tz"]))->toArray(),
			"subregion" => (new StateField([
				"alias" => "subregion",
				"description" => "The state this location is in",
			]))->toArray(),
			"recentUsers" => (new UserField([
				"isList" => true,
				"description" =>
					"The users that last logged into this location",
			]))->toArray(),
			"paymentPlatformActivations" => (new PaymentPlatformActivationField(
				["isList" => true],
			))->toArray(),
			"clearentTerminals" => (new ClearentTerminalField([
				"isList" => true,
			]))->toArray(),
			"imageName" => [
				"type" => Type::string(),
				"description" => "The S3 path for the image",
				"alias" => "image_name",
			],
			"imageUrl" => [
				"type" => Type::string(),
				"description" => "The S3 path for the image",
				"selectable" => false,
				"alias" => "image_url",
			],
			"emailVerified" => [
				"type" => Type::boolean(),
				"description" => "Location has verified the given email",
				"alias" => "email_identity_verified",
			],
			"phone1" => [
				"type" => Type::string(),
				"description" => "First phone number of location",
			],
			"phone2" => [
				"type" => Type::string(),
				"description" => "Second phone number of location",
			],
			"phone3" => [
				"type" => Type::string(),
				"description" => "Third phone number of location",
			],
			"fax" => [
				"type" => Type::string(),
				"description" => "Fax phone number of location",
			],
			"autoSave" => [
				"type" => Type::boolean(),
				"description" => "Chart auto-save enabled",
				"selectable" => false,
				"alias" => "auto_save",
			],
			"antechActive" => [
				"type" => Type::boolean(),
				"description" => "Antech integration is enabled",
				"selectable" => false,
				"alias" => "antech_active",
			],
			"antechAccountId" => [
				"type" => Type::string(),
				"description" => "Antech Account ID",
				"selectable" => false,
				"alias" => "antech_account_id",
			],
			"antechClinicId" => [
				"type" => Type::string(),
				"description" => "Antech Clinic ID",
				"selectable" => false,
				"alias" => "antech_clinic_id",
			],
			"antechUsername" => [
				"type" => Type::string(),
				"description" => "Antech client username",
				"selectable" => false,
				"alias" => "antech_username",
			],
			"antechPassword" => [
				"type" => Type::string(),
				"description" => "Antech client password",
				"selectable" => false,
				"alias" => "antech_password",
			],
			"antechPasswordHas" => [
				"type" => Type::boolean(),
				"description" => "Antech client password is set",
				"selectable" => false,
				"alias" => "antech_password_has",
			],
			"zoetisActive" => [
				"type" => Type::boolean(),
				"description" => "Zoetis integration is enabled",
				"selectable" => false,
				"alias" => "zoetis_active",
			],
			"zoetisFuseId" => [
				"type" => Type::string(),
				"description" => "Zoetis FUSE Id",
				"selectable" => false,
				"alias" => "zoetis_fuse_id",
			],
		];
	}
}
