<?php

namespace App\Models;

use App\GraphQL\Types\HistoryChartGraphQLType;

class HistoryChart extends HippoChart
{
	public static $graphQLType = HistoryChartGraphQLType::class;

	protected $table = "tblChartHistory";

	protected $chartType = "history";

	protected $fillable = [
		"user_id",
		"client_id",
		"organization_id",
		"location_id",
		"vs_ht",
		"vs_wt",
		"vs_temp",
		"vs_pulse",
		"vs_rr",
		"vs_blood_press",
		"cc",
		"note",
		"date",
		"seen_by",
		"signed",
		"visit_timer",
		"vs_mm",
		"vs_hs",
		"vs_crr",
		"removed",
		"timestamp",
		"signed_by_original",
		"signed_by_last",
		"signed_time_original",
		"signed_time_last",
		"last_updated",
	];
}
