<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\EmailChartField;
use App\GraphQL\Fields\HistoryChartField;
use App\GraphQL\Fields\PhoneChartField;
use App\GraphQL\Fields\ProgressChartField;
use App\GraphQL\Fields\SoapChartField;
use App\GraphQL\Fields\TreatmentChartField;
use App\Models\HydrationStatus;
use GraphQL\Type\Definition\Type;

class HydrationStatusGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "hydrationStatus";

	protected $attributes = [
		"name" => "HydrationStatus",
		"description" => "An available option for hydration status",
		"model" => HydrationStatus::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the hydration status",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The hydration status name",
				"alias" => "label",
			],
			"abbreviation" => [
				"type" => Type::string(),
				"description" => "The shorthand form of the status",
				"alias" => "abbr",
			],
			"soapCharts" => (new SoapChartField([
				"isList" => true,
				"description" =>
					"Soap Charts where patients presented with this status",
			]))->toArray(),
			"historyCharts" => (new HistoryChartField([
				"isList" => true,
				"description" =>
					"History charts where patients presented with this status",
			]))->toArray(),
			"phoneCharts" => (new PhoneChartField([
				"isList" => true,
				"description" =>
					"Phone charts where patients presented with this status",
			]))->toArray(),
			"progressCharts" => (new ProgressChartField([
				"isList" => true,
				"description" =>
					"Progress charts where patients presented with this status",
			]))->toArray(),
			"emailCharts" => (new EmailChartField([
				"isList" => true,
				"description" =>
					"Email charts where patients presented with this status",
			]))->toArray(),
			"treatmentCharts" => (new TreatmentChartField([
				"isList" => true,
				"description" =>
					"Treatment charts where patients presented with this status",
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
