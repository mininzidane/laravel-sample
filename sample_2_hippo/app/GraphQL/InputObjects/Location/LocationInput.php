<?php
namespace App\GraphQL\InputObjects\Location;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\LocationGraphQLType;
use GraphQL\Type\Definition\Type;

class LocationInput extends HippoInputType
{
	protected $attributes = [
		"name" => "locationInput",
		"description" => "Location for CRUD operations",
	];

	protected $requiredFields = ["name"];
	protected $graphQLType = LocationGraphQLType::class;

	public function fields(): array
	{
		return [
			"name" => [
				"name" => "name",
				"type" => Type::string(),
				"description" => "The name of the location",
			],
			"email" => [
				"name" => "email",
				"type" => Type::string(),
				"description" => "",
			],
			"organizationId" => [
				"name" => "organizationId",
				"type" => Type::int(),
				"description" =>
					"The ID of the organization this location is a part of",
				"alias" => "organization_id",
			],
			"primary" => [
				"name" => "primary",
				"type" => Type::boolean(),
				"description" =>
					"Indicates the location is the primary one in the practice",
			],
			"address1" => [
				"name" => "address1",
				"type" => Type::string(),
				"description" => "Street address line 1",
			],
			"address2" => [
				"name" => "address2",
				"type" => Type::string(),
				"description" => "Street address line 2",
			],
			"address3" => [
				"name" => "address3",
				"type" => Type::string(),
				"description" => "Street address line 3",
			],
			"city" => [
				"name" => "city",
				"type" => Type::string(),
				"description" => "City of the location",
			],
			"state" => [
				"name" => "state",
				"type" => Type::int(),
				"description" => "ID of the state(subregion)",
			],
			"zip" => [
				"name" => "zip",
				"type" => Type::string(),
				"description" => "Postal code of the location",
			],
			"phone1" => [
				"name" => "phone1",
				"type" => Type::string(),
				"description" => "First phone number of location",
			],
			"phone2" => [
				"name" => "phone2",
				"type" => Type::string(),
				"description" => "Second phone number of location",
			],
			"phone3" => [
				"name" => "phone3",
				"type" => Type::string(),
				"description" => "Third phone number of location",
			],
			"fax" => [
				"name" => "fax",
				"type" => Type::string(),
				"description" => "Fax phone number of location",
			],
			"timezone" => [
				"name" => "timezone",
				"type" => Type::int(),
				"description" => "ID of the timezone",
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
			"antechPasswordChanged" => [
				"type" => Type::boolean(),
				"description" => "Antech password has been updated",
				"selectable" => false,
				"alias" => "antech_password_changed",
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
			"imageName" => [
				"type" => Type::string(),
				"description" => "The S3 path for the image",
				"alias" => "image_name",
			],
		];
	}
}
