<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\PhoneChart;

use App\Models\PhoneChart;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PhoneChartFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PhoneChart $model */
		$model = PhoneChart::factory()->create();
		$modelNote = $model->getAttributes()["note"];

		$this->assertDatabaseHas($model->getTable(), [
			"user_id" => $model->user_id,
			"client_id" => $model->client_id,
			"organization_id" => $model->organization_id,
			"location_id" => $model->location_id,
			"vs_ht" => $model->vs_ht,
			"vs_wt" => $model->vs_wt,
			"vs_temp" => $model->vs_temp,
			"vs_pulse" => $model->vs_pulse,
			"vs_rr" => $model->vs_rr,
			"cc" => $model->cc,
			"note" => $modelNote,
			"date" => $model->date->format("Y-m-d H:i:s"),
			"signed" => $model->signed,
		]);
	}
}
