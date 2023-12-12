<?php

namespace App\Models;

use App\GraphQL\Types\PhoneChartGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property int $client_id
 * @property int $organization_id
 * @property int $location_id
 * @property float $vs_ht
 * @property float $vs_wt
 * @property float $vs_temp
 * @property float $vs_pulse
 * @property float $vs_rr
 * @property string $vs_blood_press
 * @property string $cc
 * @property string $note
 * @property Carbon $date
 * @property int $seen_by
 * @property bool $signed
 * @property int $visit_timer
 * @property int $vs_mm
 * @property int $vs_hs
 * @property int $vs_crr
 * @property bool $removed
 * @property string $timestamp
 * @property int $signed_by_original
 * @property int $signed_by_last
 * @property Carbon $signed_time_original
 * @property Carbon $signed_time_last
 * @property Carbon $last_updated
 *
 * @mixin \Eloquent
 */
class PhoneChart extends HippoChart
{
	use HasFactory;

	public static $graphQLType = PhoneChartGraphQLType::class;

	protected $table = "tblChartPhone";

	protected $chartType = "phone";

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
