<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\EventRecurSkip;

use App\Models\EventRecurSkip;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class EventRecurSkipFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var EventRecurSkip $model */
		$model = EventRecurSkip::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"recur_id" => $model->recur_id,
			"start_time" => $model->start_time,
		]);
	}
}
