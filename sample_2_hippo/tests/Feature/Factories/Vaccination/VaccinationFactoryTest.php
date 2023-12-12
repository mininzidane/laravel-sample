<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Vaccination;

use App\Models\Vaccination;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class VaccinationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Vaccination $model */
		$model = Vaccination::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"client_id" => $model->client_id,
			"current_owner_id" => $model->current_owner_id,
			"current_gender" => $model->current_gender,
			"current_weight" => $model->current_weight,
			"item_kit_id" => $model->item_kit_id,
			"reminder_id" => $model->reminder_id,
			"reminder_item_id" => $model->reminder_item_id,
			"vaccine_item_id" => $model->vaccine_item_id,
			"vaccine_name" => $model->vaccine_name,
			"dosage" => $model->dosage,
			"receiving_item_lot_number" => $model->receiving_item_lot_number,
			"receiving_item_expiration_date" => $model->receiving_item_expiration_date->format(
				"Y-m-d",
			),
			"serialnumber" => $model->serialnumber,
			"timestamp" => $model->timestamp->format("Y-m-d H:i:s"),
			"seen_by" => $model->seen_by,
			"removed" => $model->removed,
			"administered_date" => $model->administered_date->format("Y-m-d"),
			"location_administered" => $model->location_administered,
			"last_modified" => $model->last_modified->format("Y-m-d H:i:s"),
			"last_modified_user" => $model->last_modified_user,
			"administered_by" => $model->administered_by,
			"manufacturer_supplier" => $model->manufacturer_supplier,
		]);
	}
}
