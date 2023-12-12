<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Reminder;

use App\Models\Reminder;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReminderFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Reminder $model */
		$model = Reminder::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"organization_id" => $model->organization_id,
			"location_id" => $model->location_id,
			"client_id" => $model->client_id,
			"item_id" => $model->item_id,
			"invoice_id" => $model->invoice_id,
			"invoice_item_id" => $model->invoice_item_id,
			"description" => $model->description,
			"frequency" => $model->frequency,
			"start_date" => $model->start_date->format("Y-m-d"),
			"due_date" => $model->due_date->format("Y-m-d"),
			"email_sent" => $model->email_sent->format("Y-m-d H:i:s"),
			"removed_datetime" => $model->removed
				? $model->removed_datetime->format("Y-m-d H:i:s")
				: null,
			"removed" => $model->removed,
		]);
	}
}
