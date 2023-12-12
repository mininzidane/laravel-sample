<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\TreatmentSheetTreatment;

use App\Models\TreatmentSheetTreatment;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class TreatmentSheetTreatmentFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var TreatmentSheetTreatment $model */
		$model = TreatmentSheetTreatment::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"schedule_event_id" => $model->schedule_event_id,
			"treatment_name" => $model->treatment_name,
			"client_id" => $model->client_id,
			"item_id" => $model->item_id,
			"sale_id" => $model->sale_id,
			"line" => $model->line,
			"qty" => $model->qty,
			"assign_to_user_id" => $model->assign_to_user_id,
			"due" => $model->due,
			"removed_reason" => $model->removed_reason,
			"rejected_reason" => $model->rejected_reason,
			"rejected" => $model->rejected,
			"completed" => $model->completed,
			"completed_time" => $model->completed_time,
			"chart_note" => $model->chart_note,
			"recur" => $model->recur,
			"recur_next_due" => $model->recur_next_due,
			"removed" => $model->removed,
		]);
	}
}
