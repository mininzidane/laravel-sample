<?php

namespace Tests\Feature\Factories\Receiving;

use App\Models\Receiving;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Receiving $model */
		$model = Receiving::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"status_id" => $model->status_id,
			"location_id" => $model->location_id,
			"supplier_id" => $model->supplier_id,
			"user_id" => $model->user_id,
			"active" => $model->active,
			"comment" => $model->comment,
		]);
	}
}
