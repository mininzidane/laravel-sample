<?php

namespace App\Models;

use App\GraphQL\Types\HippoChartInterfaceGraphQLType;
use App\Models\Scopes\ChartScope;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class HippoChart extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;

	public static $graphQLType = HippoChartInterfaceGraphQLType::class;

	protected $appends = ["chart_type"];

	protected $chartType = "none";

	public static function boot()
	{
		parent::boot();

		static::addGlobalScope(new ChartScope());
	}

	public function getChartTypeAttribute()
	{
		return $this->chartType;
	}

	public function getNoteAttribute()
	{
		return null;
	}

	public function user()
	{
		return $this->belongsTo(User::class, "user_id");
	}

	public function seenBy()
	{
		return $this->belongsTo(User::class, "seen_by");
	}

	public function patient()
	{
		return $this->belongsTo(Patient::class, "client_id");
	}

	public function location()
	{
		return $this->belongsTo(Location::class, "location_id");
	}

	public function organization()
	{
		return $this->belongsTo(Organization::class, "organization_id");
	}

	public function lastSignedBy()
	{
		return $this->belongsTo(User::class, "signed_by_last");
	}

	public function originallySignedBy()
	{
		return $this->belongsTo(User::class, "signed_by_original");
	}

	public function mucousMembraneStatus()
	{
		return $this->belongsTo(MucousMembraneStatus::class, "vs_mm");
	}

	public function hydrationStatus()
	{
		return $this->belongsTo(HydrationStatus::class, "vs_hs");
	}
}
