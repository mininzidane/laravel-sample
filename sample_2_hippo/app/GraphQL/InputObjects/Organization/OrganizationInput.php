<?php

namespace App\GraphQL\InputObjects\Organization;

use App\GraphQL\InputObjects\HippoInputType;
use App\GraphQL\Types\OrganizationGraphQLType;
use GraphQL\Type\Definition\Type;

class OrganizationInput extends HippoInputType
{
	protected $attributes = [
		"name" => "organizationInput",
		"description" => "Organization for CRUD operations",
	];

	protected $requiredFields = ["name"];
	protected $graphQLType = OrganizationGraphQLType::class;

	public function fields(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::string(),
				"description" => "",
			],
			"name" => [
				"name" => "name",
				"type" => Type::string(),
				"description" => "The name of the organization",
			],
			"ein" => [
				"type" => Type::string(),
				"description" => "EIN",
			],
			"units" => [
				"name" => "units",
				"type" => Type::int(),
				"description" => "",
			],
			"currencySymbol" => [
				"type" => Type::string(),
				"description" =>
					"The currency symbol used for this organization",
				"alias" => "currency_symbol",
			],
			"lineItemRounding" => [
				"type" => Type::string(),
				"description" =>
					"The currency symbol used for this organization",
				"alias" => "line_item_rounding",
			],
			"phrActive" => [
				"type" => Type::boolean(),
				"description" => "PHR is active for organization",
				"alias" => "phr_active",
			],
			"vcpActive" => [
				"type" => Type::boolean(),
				"description" => "The health of the VCP account",
				"selectable" => false,
				"alias" => "vcp_active",
			],
			"vcpUserName" => [
				"type" => Type::string(),
				"description" => "The username of the VCP account",
				"selectable" => false,
				"alias" => "vcp_username",
			],
			"vcpPassword" => [
				"type" => Type::string(),
				"description" => "The password of the VCP account",
				"selectable" => false,
				"alias" => "vcp_password",
			],
			"vcpPasswordChanged" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the VCP password has been changed",
				"selectable" => false,
				"alias" => "vcp_password_changed",
			],
			"idexxActive" => [
				"type" => Type::boolean(),
				"description" => "IDEXX integration is enabled",
				"selectable" => false,
				"alias" => "idexx_active",
			],
			"idexxUsername" => [
				"type" => Type::string(),
				"description" => "IDEXX username",
				"selectable" => false,
				"alias" => "idexx_username",
			],
			"idexxPassword" => [
				"type" => Type::string(),
				"description" => "IDEXX password",
				"selectable" => false,
				"alias" => "idexx_password",
			],
			"idexxPasswordChanged" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the Idexx password has been changed",
				"selectable" => false,
				"alias" => "idexx_password_changed",
			],
			"accountStatus" => [
				"type" => Type::int(),
				"description" => "The organization account status.",
				"selectable" => false,
				"alias" => "account_status",
			],
			"trialExpired" => [
				"type" => Type::boolean(),
				"description" => "Whether the organization trial is expired",
				"selectable" => false,
				"alias" => "trial_expired",
			],
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
			"estimateStatement" => [
				"type" => Type::string(),
				"description" => "Description of the estimate verbiage",
				"selectable" => false,
				"alias" => "estimate_statement",
			],
			"paymentInfo" => [
				"type" => Type::string(),
				"description" => "Description of the payment info verbiage",
				"selectable" => false,
				"alias" => "payment_info",
			],
			"returnPolicy" => [
				"type" => Type::string(),
				"description" => "Description of the return policy verbiage",
				"selectable" => false,
				"alias" => "return_policy",
			],
		];
	}
}
