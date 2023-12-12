<?php

namespace Tests\Feature\Factories\PatientAllergyNote;

use App\Models\Patient;
use App\Models\PatientAllergyNote;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientAllergyFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var PatientAllergyNote $model */
		$model = PatientAllergyNote::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"client_id" => $model->client_id,
			"note" => $model->note,
		]);
	}
}
