<?php

namespace App\Models;

use App\GraphQL\Types\EmailChartGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
 * @property string $date
 * @property int $seen_by
 * @property int $signed
 * @property int $visit_timer
 * @property int $vs_mm
 * @property int $vs_hs
 * @property string $vs_crr
 * @property int $removed
 * @property int $timestamp
 * @property int $signed_by_original
 * @property int $signed_by_last
 * @property string $signed_time_original
 * @property string $signed_time_last
 * @property string $last_updated
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class EmailChart extends HippoChart
{
	use HasFactory;

	public static $graphQLType = EmailChartGraphQLType::class;
	protected $table = "tblChartEmail";
	protected $chartType = "email";

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

	public function invoiceItems(): MorphMany
	{
		return $this->morphMany(InvoiceItem::class, "chart");
	}
}
