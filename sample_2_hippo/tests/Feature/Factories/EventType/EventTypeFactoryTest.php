<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\EventType;

use App\Models\EventType;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class EventTypeFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var EventType $model */
		$model = EventType::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"name" => $model->name,
			"description" => $model->description,
			"color" => $model->color,
			"organization_id" => $model->organization->id,
		]);
	}
}
