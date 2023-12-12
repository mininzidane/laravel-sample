<?php

namespace Tests\Feature\Factories\Log;

use App\Models\Log;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LogFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Log $model */
		$model = Log::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"organization_id" => $model->organization_id,
			"location_id" => $model->location_id,
			"user_id" => $model->user_id,
			"action_id" => $model->action_id,
			"affected_id" => $model->affected_id,
			"information" => $model->information,
			"screen" => $model->screen,
		]);
	}
}
