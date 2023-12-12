<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\HydrationStatusField;
use App\GraphQL\Fields\LocationField;
use App\GraphQL\Fields\MucousMembraneStatusField;
use App\GraphQL\Fields\OrganizationField;
use App\GraphQL\Fields\PatientField;
use App\GraphQL\Fields\UserField;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InterfaceType;

class HippoChartInterfaceGraphQLType extends InterfaceType
{
	public static $graphQLType = "chartInterface";

	protected $attributes = [
		"name" => "ChartInterface",
		"description" => "Hippo chart interface",
	];

	public static function getGraphQLTypeName()
	{
		return static::$graphQLType;
	}

	public function fields(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::int()),
				"description" => "",
			],
			"createdAt" => [
				"type" => Type::string(),
				"description" => "",
			],
			"updatedAt" => [
				"type" => Type::string(),
				"description" => "",
			],
			"chartType" => [
				"type" => Type::string(),
				"description" => "The type of chart associated",
			],
			"height" => [
				"type" => Type::string(),
				"description" => "The height of the patient",
				"alias" => "vs_ht",
			],
			"weight" => [
				"type" => Type::string(),
				"description" => "The weight of the patient",
				"alias" => "vs_wt",
			],
			"temperature" => [
				"type" => Type::string(),
				"description" => "The temperature of the patient",
				"alias" => "vs_temp",
			],
			"pulse" => [
				"type" => Type::string(),
				"description" => "The pulse of the patient",
				"alias" => "vs_pulse",
			],
			"respirationRate" => [
				"type" => Type::string(),
				"description" => "The respiration rate of the patient",
				"alias" => "vs_rr",
			],
			"bloodPressure" => [
				"type" => Type::string(),
				"description" => "The blood pressure of the patient",
				"alias" => "vs_blood_press",
			],
			"chiefComplaint" => [
				"type" => Type::string(),
				"description" => "The chief complaint for the chart",
				"alias" => "cc",
			],
			"signed" => [
				"type" => Type::boolean(),
				"description" => "The signing status of the chart",
				"alias" => "signed",
			],
			"visitTimer" => [
				"type" => Type::string(),
				"description" => "The duration of the visit",
				"alias" => "visit_timer",
			],
			"capillaryRefillRate" => [
				"type" => Type::string(),
				"description" => "The capillary refill rate of the patient",
				"alias" => "vs_crr",
			],
			"originallySignedAt" => [
				"type" => Type::string(),
				"description" => "When the chart was originally signed",
				"alias" => "signed_time_original",
			],
			"lastSignedAt" => [
				"type" => Type::string(),
				"description" => "When the chart was last signed",
				"alias" => "signed_time_last",
			],
			"user" => (new UserField())->toArray(),
			"seenBy" => (new UserField([
				"description" => "The vet who saw the patient",
			]))->toArray(),
			"patient" => (new PatientField([
				"description" => "The patient seen in the chart",
			]))->toArray(),
			"organization" => (new OrganizationField([
				"description" =>
					"The organization that the patient is associated with",
			]))->toArray(),
			"location" => (new LocationField([
				"description" =>
					"The practice location the chart was created at",
			]))->toArray(),
			"originallySignedBy" => (new UserField([
				"description" => "The vet that originally signed the chart",
			]))->toArray(),
			"lastSignedBy" => (new UserField([
				"description" => "The vet that last signed the chart",
			]))->toArray(),
			"mucousMembraneStatus" => (new MucousMembraneStatusField([
				"description" =>
					"The status of the mucous membranes of the patient",
			]))->toArray(),
			"hydrationStatus" => (new HydrationStatusField([
				"description" => "The hydration status of the patient",
			]))->toArray(),
			"date" => [
				"type" => Type::string(),
				"description" =>
					"By default the created date but can be backdated",
			],
		];
	}

	public function resolveType($root)
	{
		$type = $root["chart_type"];

		if ($type === "soap") {
			return GraphQL::type("soapChart");
		} elseif ($type === "email") {
			return GraphQL::type("emailChart");
		} elseif ($type === "progress") {
			return GraphQL::type("progressChart");
		} elseif ($type === "phone") {
			return GraphQL::type("phoneChart");
		} elseif ($type === "history") {
			return GraphQL::type("historyChart");
		} elseif ($type === "treatment") {
			return GraphQL::type("treatmentChart");
		} else {
			return GraphQL::type("hippoChart");
		}
	}
}
