<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\EmailChartField;
use App\GraphQL\Fields\HistoryChartField;
use App\GraphQL\Fields\PhoneChartField;
use App\GraphQL\Fields\ProgressChartField;
use App\GraphQL\Fields\SoapChartField;
use App\GraphQL\Fields\TreatmentChartField;
use App\Models\MucousMembraneStatus;
use GraphQL\Type\Definition\Type;

class MucousMembraneStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "mucousMembraneStatus";

	protected $attributes = [
		"name" => "MucousMembraneStatus",
		"description" => "An available option for mucous membrane status",
		"model" => MucousMembraneStatus::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the mucous membrane status",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The mucous membrane status name",
				"alias" => "label",
			],
			"abbreviation" => [
				"type" => Type::string(),
				"description" => "The shorthand form of the status",
				"alias" => "abbr",
			],
			"soapCharts" => (new SoapChartField([
				"description" =>
					"Soap Charts where patients presented with this status",
				"isList" => true,
			]))->toArray(),
			"historyCharts" => (new HistoryChartField([
				"description" =>
					"History charts where patients presented with this status",
				"isList" => true,
			]))->toArray(),
			"phoneCharts" => (new PhoneChartField([
				"description" =>
					"Phone charts where patients presented with this status",
				"isList" => true,
			]))->toArray(),
			"progressCharts" => (new ProgressChartField([
				"description" =>
					"Progress charts where patients presented with this status",
				"isList" => true,
			]))->toArray(),
			"emailCharts" => (new EmailChartField([
				"description" =>
					"Email charts where patients presented with this status",
				"isList" => true,
			]))->toArray(),
			"treatmentCharts" => (new TreatmentChartField([
				"description" =>
					"Treatment charts where patients presented with this status",
				"isList" => true,
			]))->toArray(),
			"referenceCount" => [
				"type" => Type::string(),
				"selectable" => false,
				"description" => "The count of references to this status",
				"alias" => "reference_count",
			],
		];
	}
}
