<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\AppointmentField;
use App\GraphQL\Fields\DispensationField;
use App\GraphQL\Fields\EmailChartField;
use App\GraphQL\Fields\HistoryChartField;
use App\GraphQL\Fields\InvoiceItemField;
use App\GraphQL\Fields\LineItemField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\PermissionField;
use App\GraphQL\Fields\PhoneChartField;
use App\GraphQL\Fields\PrescriptionField;
use App\GraphQL\Fields\ProgressChartField;
use App\GraphQL\Fields\ReceivingLegacyField;
use App\GraphQL\Fields\ResourceField;
use App\GraphQL\Fields\RoleField;
use App\GraphQL\Fields\SoapChartField;
use App\GraphQL\Fields\SupplierLegacyField;
use App\GraphQL\Fields\TreatmentChartField;
use App\GraphQL\Fields\TreatmentSheetTreatmentField;
use App\Models\User;
use GraphQL\Type\Definition\Type;

class UserGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "user";

	protected $attributes = [
		"name" => "User",
		"description" => "A Hippo Manager application user.",
		"model" => User::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the resource",
			],
			"username" => [
				"type" => Type::string(),
				"description" => 'The user\'s username',
			],
			"firstName" => [
				"type" => Type::string(),
				"description" => 'The user\'s first name',
				"alias" => "first_name",
			],
			"lastName" => [
				"type" => Type::string(),
				"description" => 'The user\'s last name',
				"alias" => "last_name",
			],
			"fullName" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => 'The user\'s full name',
				"alias" => "full_name",
			],
			"phone" => [
				"type" => Type::string(),
				"description" => 'The user\'s phone number',
				"alias" => "phone1",
			],
			"email" => [
				"type" => Type::string(),
				"description" => 'The user\'s primary work email address',
			],
			"emailVerified" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether the user has verified their email address",
				"alias" => "email_verified",
			],
			"specialty" => [
				"type" => Type::string(),
				"description" => 'The user\'s medical specialty',
			],
			"active" => [
				"type" => Type::boolean(),
				"description" => "Whether or not the user is active",
			],
			"degree" => [
				"type" => Type::string(),
				"description" => "The degree held by this user",
			],
			"providerType" => [
				"type" => Type::string(),
				"description" => "Same as degree",
				"alias" => "degree",
			],
			"isProvider" => [
				"type" => Type::boolean(),
				"description" =>
					"Whether or not the user is a veterinary provider",
			],
			"clientedUsername" => [
				"type" => Type::string(),
				"description" => 'The user\'s decrypted ClientEd user name',
				"alias" => "cliented_username",
			],
			"clientedPassword" => [
				"type" => Type::string(),
				"description" => 'The user\'s decrypted ClientEd password',
				"alias" => "cliented_password",
			],
			"resources" => (new ResourceField([
				"isList" => true,
				"description" => 'The user\'s available resources',
			]))->toArray(),
			"prescriptions" => (new PrescriptionField([
				"isList" => true,
			]))->toArray(),
			"lineItems" => (new LineItemField([
				"isList" => true,
				"description" =>
					"The line items that have been parts of sales by this user",
			]))->toArray(),
			"addedPatients" => (new PatientField([
				"isList" => true,
				"description" => "Patients added by this user",
			]))->toArray(),
			"appointments" => (new AppointmentField([
				"isList" => true,
				"description" => "Appointments for this user",
			]))->toArray(),
			"createdAppointments" => (new AppointmentField([
				"isList" => true,
				"description" => "Appointments created by this user",
			]))->toArray(),
			"soapCharts" => (new SoapChartField([
				"isList" => true,
				"description" => "Soap charts created by this user",
			]))->toArray(),
			"soapChartsSeen" => (new SoapChartField([
				"isList" => true,
				"description" => "Soap charts last seen by this user",
			]))->toArray(),
			"soapChartsLastSigned" => (new SoapChartField([
				"isList" => true,
				"description" => "Soap charts last signed by this user",
			]))->toArray(),
			"soapChartsOriginallySigned" => (new SoapChartField([
				"isList" => true,
				"description" => "Soap charts originally signed by this user",
			]))->toArray(),
			"historyCharts" => (new HistoryChartField([
				"isList" => true,
				"description" => "History charts created by this user",
			]))->toArray(),
			"historyChartsSeen" => (new HistoryChartField([
				"isList" => true,
				"description" => "History charts last seen by this user",
			]))->toArray(),
			"historyChartsLastSigned" => (new HistoryChartField([
				"isList" => true,
				"description" => "History charts last signed by this user",
			]))->toArray(),
			"historyChartsOriginallySigned" => (new HistoryChartField([
				"isList" => true,
				"description" =>
					"History charts originally signed by this user",
			]))->toArray(),
			"phoneCharts" => (new PhoneChartField([
				"isList" => true,
				"description" => "Phone charts created by this user",
			]))->toArray(),
			"phoneChartsSeen" => (new PhoneChartField([
				"isList" => true,
				"description" => "Phone charts last seen by this user",
			]))->toArray(),
			"phoneChartsLastSigned" => (new PhoneChartField([
				"isList" => true,
				"description" => "Phone charts last signed by this user",
			]))->toArray(),
			"phoneChartsOriginallySigned" => (new PhoneChartField([
				"isList" => true,
				"description" => "Phone charts originally signed by this user",
			]))->toArray(),
			"emailCharts" => (new EmailChartField([
				"isList" => true,
				"description" => "Email charts created by this user",
			]))->toArray(),
			"emailChartsSeen" => (new EmailChartField([
				"isList" => true,
				"description" => "Email charts last seen by this user",
			]))->toArray(),
			"emailChartsLastSigned" => (new EmailChartField([
				"isList" => true,
				"description" => "Email charts last signed by this user",
			]))->toArray(),
			"emailChartsOriginallySigned" => (new EmailChartField([
				"isList" => true,
				"description" => "Email charts originally signed by this user",
			]))->toArray(),
			"progressCharts" => (new ProgressChartField([
				"isList" => true,
				"description" => "Progress charts created by this user",
			]))->toArray(),
			"progressChartsSeen" => (new ProgressChartField([
				"isList" => true,
				"description" => "Progress charts last seen by this user",
			]))->toArray(),
			"progressChartsLastSigned" => (new ProgressChartField([
				"isList" => true,
				"description" => "Progress charts last signed by this user",
			]))->toArray(),
			"progressChartsOriginallySigned" => (new ProgressChartField([
				"isList" => true,
				"description" =>
					"Progress charts originally signed by this user",
			]))->toArray(),
			"treatmentCharts" => (new TreatmentChartField([
				"isList" => true,
				"description" => "Treatment charts created by this user",
			]))->toArray(),
			"treatmentChartsSeen" => (new TreatmentChartField([
				"isList" => true,
				"description" => "Treatment charts last seen by this user",
			]))->toArray(),
			"treatmentChartsLastSigned" => (new TreatmentChartField([
				"isList" => true,
				"description" => "Treatment charts last signed by this user",
			]))->toArray(),
			"treatmentChartsOriginallySigned" => (new TreatmentChartField([
				"isList" => true,
				"description" =>
					"Treatment charts originally signed by this user",
			]))->toArray(),
			"invoiceItems" => new InvoiceItemField([
				"isList" => true,
				"description" => "Invoice Items associated with this provider",
			]),
			"receivings" => (new ReceivingLegacyField([
				"isList" => true,
				"description" => "Receivings run by this user",
			]))->toArray(),
			"suppliers" => (new SupplierLegacyField([
				"isList" => true,
				"description" => "The suppliers configured by this user",
			]))->toArray(),
			"dispensations" => (new DispensationField([
				"isList" => true,
				"description" => "Dispensations created by this user",
			]))->toArray(),
			"originallySignedDispensations" => (new DispensationField([
				"isList" => true,
				"description" => "Dispensations originally signed by this user",
			]))->toArray(),
			"lastSignedDispensations" => (new DispensationField([
				"isList" => true,
				"description" => "Dispensations last signed by this user",
			]))->toArray(),
			"lastClient" => new PatientField([
				"description" => "The last patient access by this provider",
			]),
			"lastLocation" => new LocationField([
				"description" => "The last location logged into by the user",
				"restrictions" => [
					"in" => ["locations"],
				],
			]),
			"locations" => new LocationField([
				"isList" => true,
				"description" => "The locations the user has access to",
			]),
			"organization" => new OrganizationField([
				"description" => "The organization the user is associated with",
			]),
			"permissions" => (new PermissionField([
				"description" => "The permissions held by a user",
				"isList" => true,
			]))->toArray(),
			"roles" => (new RoleField([
				"description" => "The roles held by a user",
				"isList" => true,
			]))->toArray(),
			"treatmentSheetTreatments" => (new TreatmentSheetTreatmentField([
				"isList" => true,
				"description" => "The associated treatment sheet treatments",
			]))->toArray(),
			"licenseNumber" => [
				"type" => Type::string(),
				"description" => 'The user\'s license number',
				"alias" => "license",
			],
			"ein" => [
				"type" => Type::string(),
				"description" => "The EIN for the user",
			],
			"dea" => [
				"type" => Type::string(),
				"description" => "The DEA number for the user",
			],
			"landing" => [
				"type" => Type::string(),
				"description" => "The user's choosen landing page",
			],
		];
	}
}
