<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\EventRecur;

use App\Models\EventRecur;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class EventRecurFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var EventRecur $model */
		$model = EventRecur::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"schedule_event_id" => $model->schedule_event_id,
			"repeats" => $model->repeats,
			"repeats_every" => $model->repeats_every,
			"repeat_by" => $model->repeat_by,
			"start_date" => $model->start_date,
			"end_type" => $model->end_type,
			"end_date" => $model->end_date,
			"end_on" => $model->end_on,
		]);
	}
}
