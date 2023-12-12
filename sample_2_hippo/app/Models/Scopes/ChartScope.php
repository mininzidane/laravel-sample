<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ChartScope implements Scope
{
	protected $chartFields = [
		"id",
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
		"date",
		"seen_by",
		"signed",
		"visit_timer",
		"vs_mm",
		"vs_hs",
		"vs_crr",
		"signed_by_original",
		"signed_by_last",
		"signed_time_original",
		"signed_time_last",
		"last_updated",
		"created_at",
		"updated_at",
		"deleted_at",
	];

	public function apply(Builder $builder, Model $model)
	{
		if (empty($builder->getQuery()->columns)) {
			$builder->addSelect($this->chartFields);
		}
	}
}
