<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Event;

use App\Models\Event;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class EventFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Event $model */
		$model = Event::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"resource_id" => $model->resource_id,
			"organization_id" => $model->organization_id,
			"name" => $model->name,
			"description" => $model->description,
			"length" => $model->length,
			"type_id" => $model->type_id,
			"note_type" => $model->note_type,
		]);
	}
}
