<?php

namespace Tests\Feature\Factories\SoapChart;

use App\Models\SoapChart;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class SoapChartFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var SoapChart $model */
		$model = SoapChart::factory()->create();

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
			"soap_s" => $model->soap_s,
			"soap_o" => $model->soap_o,
			"ros_constitutional_symptoms" =>
				$model->ros_constitutional_symptoms,
			"ros_eyes" => $model->ros_eyes,
			"ros_enmt" => $model->ros_enmt,
			"ros_cardio" => $model->ros_cardio,
			"ros_respiratory" => $model->ros_respiratory,
			"ros_gastro" => $model->ros_gastro,
			"ros_genitourinary" => $model->ros_genitourinary,
			"ros_integumentary" => $model->ros_integumentary,
			"ros_musculoskeletal" => $model->ros_musculoskeletal,
			"ros_neurological" => $model->ros_neurological,
			"ros_behavioral" => $model->ros_behavioral,
			"ros_endocrine" => $model->ros_endocrine,
			"ros_homo_lymph" => $model->ros_homo_lymph,
			"ros_allergic_immuno" => $model->ros_allergic_immuno,
			"soap_a" => $model->soap_a,
			"soap_p" => $model->soap_p,
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
