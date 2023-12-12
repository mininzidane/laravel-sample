<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\GraphQL\Types\HydrationStatusGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

class HydrationStatus extends HippoModel
{
	use HasTimestamps;
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = HydrationStatusGraphQLType::class;

	protected $table = "tblHydrationStatusOptions";

	protected $fillable = ["label", "abbr"];

	protected $appends = ["reference_count"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["label"];

		parent::__construct($attributes);
	}

	public function treatmentCharts()
	{
		return $this->hasMany(TreatmentChart::class, "vs_hs");
	}

	public function soapCharts()
	{
		return $this->hasMany(SoapChart::class, "vs_hs");
	}

	public function progressCharts()
	{
		return $this->hasMany(ProgressChart::class, "vs_hs");
	}

	public function HistoryCharts()
	{
		return $this->hasMany(HistoryChart::class, "vs_hs");
	}

	public function phoneCharts()
	{
		return $this->hasMany(PhoneChart::class, "vs_hs");
	}

	public function emailCharts()
	{
		return $this->hasMany(EmailChart::class, "vs_hs");
	}

	public function getReferenceCountAttribute()
	{
		return $this->treatmentCharts()->count() +
			$this->soapCharts()->count() +
			$this->progressCharts()->count() +
			$this->HistoryCharts()->count() +
			$this->phoneCharts()->count() +
			$this->emailCharts()->count();
	}
}
