<?php

namespace Tests\Feature\Factories\ProgressChart;

use App\Models\ProgressChart;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ProgressChartFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var ProgressChart $model */
		$model = ProgressChart::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"user_id" => $model->user_id,
			"client_id" => $model->client_id,
			"organization_id" => $model->organization_id,
			"location_id" => $model->location_id,
			"vs_ht" => $model->vs_ht,
			"vs_wt" => $model->vs_wt,
			"vs_temp" => $model->vs_temp,
			"vs_pulse" => $model->vs_pulse,
			"vs_rr" => $model->vs_rr,
			"vs_blood_press" => $model->vs_blood_press,
			"cc" => $model->cc,
			"note" => "",
			"date" => $model->date->format(self::DATETIME_FORMAT),
			"seen_by" => $model->seen_by,
			"signed" => $model->signed,
			"visit_timer" => $model->visit_timer,
			"vs_mm" => $model->vs_mm,
			"vs_hs" => $model->vs_hs,
			"vs_crr" => $model->vs_crr,
			"removed" => $model->removed,
			"signed_by_original" => $model->signed_by_original,
			"signed_by_last" => $model->signed_by_last,
			"signed_time_original" => $model->signed_time_original->format(
				self::DATETIME_FORMAT,
			),
			"signed_time_last" => $model->signed_time_last->format(
				self::DATETIME_FORMAT,
			),
			"last_updated" => $model->last_updated->format(
				self::DATETIME_FORMAT,
			),
		]);
	}
}
