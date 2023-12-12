<?php

namespace App\Models;

use App\GraphQL\Types\SoapChartGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\SoapChart
 *
 * @property-read int $id
 *
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
 * @property string $soap_s
 * @property string $soap_o
 * @property bool $ros_constitutional_symptoms
 * @property bool $ros_eyes
 * @property bool $ros_enmt
 * @property bool $ros_cardio
 * @property bool $ros_respiratory
 * @property bool $ros_gastro
 * @property bool $ros_genitourinary
 * @property bool $ros_integumentary
 * @property bool $ros_musculoskeletal
 * @property bool $ros_neurological
 * @property bool $ros_behavioral
 * @property bool $ros_endocrine
 * @property bool $ros_homo_lymph
 * @property bool $ros_allergic_immuno
 * @property string $soap_a
 * @property string $soap_p
 * @property Carbon $date
 * @property int $seen_by
 * @property bool $signed
 * @property string $visit_timer
 * @property int $vs_mm
 * @property int $vs_hs
 * @property float $vs_crr
 * @property bool $removed
 * @property Carbon $timestamp
 * @property int $signed_by_original
 * @property int $signed_by_last
 * @property Carbon $signed_time_original
 * @property Carbon $signed_time_last
 * @property Carbon $last_updated
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Collection|InvoiceItem[] $invoiceItems
 */
class SoapChart extends HippoChart
{
	use HasFactory;

	public static $graphQLType = SoapChartGraphQLType::class;

	protected $table = "tblChartSoap";

	protected $chartType = "soap";

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
		"soap_s",
		"soap_o",
		"ros_constitutional_symptoms",
		"ros_eyes",
		"ros_enmt",
		"ros_cardio",
		"ros_respiratory",
		"ros_gastro",
		"ros_genitourinary",
		"ros_integumentary",
		"ros_musculoskeletal",
		"ros_neurological",
		"ros_behavioral",
		"ros_endocrine",
		"ros_homo_lymph",
		"ros_allergic_immuno",
		"soap_a",
		"soap_p",
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
