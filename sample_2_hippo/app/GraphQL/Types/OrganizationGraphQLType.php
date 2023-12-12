<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\EmailChartField;
use App\GraphQL\Fields\HistoryChartField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OwnerField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\PhoneChartField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\ProgressChartField;
use App\GraphQL\Fields\SchedulerSettingField;
use App\GraphQL\Fields\SoapChartField;
use App\GraphQL\Fields\SupplierLegacyField;
use App\GraphQL\Fields\TreatmentChartField;
use App\Models\Organization;
use GraphQL\Type\Definition\Type;

class OrganizationGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "organization";

	protected $attributes = [
		"name" => "Organization",
		"description" => "An organization",
		"model" => Organization::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the user",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The name of the organization",
			],
			"units" => [
				"type" => Type::string(),
				"description" =>
					"The unit system used for this organization.  1 for English, 2 for Metric",
				"resolve" => function ($organization) {
					if ($organization->units == 1) {
						return "English";
					}

					return "Metric";
				},
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
			"schedulerSettings" => (new SchedulerSettingField([
				"description" =>
					"The current configuration for appointments for this organization",
			]))->toArray(),
			"locations" => (new LocationField([
				"description" => "Organization locations",
				"isList" => true,
			]))->toArray(),
			"suppliers" => (new SupplierLegacyField([
				"description" => "Suppliers configured for this organization",
				"isList" => true,
			]))->toArray(),
			"soapCharts" => (new SoapChartField([
				"description" =>
					"The soap charts created for this organization",
				"isList" => true,
			]))->toArray(),
			"historyCharts" => (new HistoryChartField([
				"description" =>
					"The history charts created for this organization",
				"isList" => true,
			]))->toArray(),
			"emailCharts" => (new EmailChartField([
				"description" =>
					"The email charts created for this organization",
				"isList" => true,
			]))->toArray(),
			"phoneCharts" => (new PhoneChartField([
				"description" =>
					"The phone charts created for this organization",
				"isList" => true,
			]))->toArray(),
			"progressCharts" => (new ProgressChartField([
				"description" =>
					"The progress charts created for this organization",
				"isList" => true,
			]))->toArray(),
			"treatmentCharts" => (new TreatmentChartField([
				"description" =>
					"The treatment charts created for this organization",
				"isList" => true,
			]))->toArray(),
			"appointments" => (new AppointmentField([
				"description" =>
					"The appointments associated with this organization",
				"isList" => true,
			]))->toArray(),
			"prescriptions" => (new PrescriptionField([
				"isList" => true,
			]))->toArray(),
			"patients" => (new PatientField(["isList" => true]))->toArray(),
			"owners" => (new OwnerField(["isList" => true]))->toArray(),
			"vcpActive" => [
				"type" => Type::boolean(),
				"description" => "The health of the VCP account",
				"selectable" => false,
				"alias" => "vcp_active",
			],
			"ein" => [
				"type" => Type::string(),
				"description" => "EIN",
			],
			"phrActive" => [
				"type" => Type::boolean(),
				"description" => "PHR is active for organization",
				"alias" => "phr_active",
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
			"vcpHasPassword" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the vcp integration has a password",
				"selectable" => false,
				"resolve" => function ($organization) {
					if ($organization->vcpPassword) {
						return true;
					}

					return false;
				},
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
			"idexxHasPassword" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the idexx integration has a password",
				"selectable" => false,
				"resolve" => function ($organization) {
					if ($organization->idexxPassword) {
						return true;
					}

					return false;
				},
			],
			"imageName" => [
				"type" => Type::string(),
				"description" => "The S3 path for the image",
				"alias" => "image_name",
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
			"imageUrl" => [
				"type" => Type::string(),
				"description" => "The S3 path for the image",
				"selectable" => false,
				"alias" => "image_url",
			],
		];
	}
}
