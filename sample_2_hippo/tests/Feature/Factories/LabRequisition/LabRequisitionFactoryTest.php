<?php

namespace Tests\Feature\Factories\LabRequisition;

use App\Models\LabRequisition;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LabRequisitionFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var LabRequisition $model */
		$model = LabRequisition::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"user_id" => $model->user_id,
			"veterinarian_id" => $model->veterinarian_id,
			"client_id" => $model->client_id,
			"location_id" => $model->location_id,
			"order_code_id" => $model->order_code_id,
			"custom_order_code" => $model->custom_order_code,
			"integration" => $model->integration,
			"status" => $model->status,
			"reviewed" => $model->reviewed,
			"removed" => $model->removed,
		]);
	}
}
