<?php

namespace Tests\Feature\Factories\PatientDrugAllergy;

use App\Models\PatientDrugAllergy;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientDrugAllergyFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var PatientDrugAllergy $model */
		$model = PatientDrugAllergy::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"allergy" => $model->allergy,
			"removed" => $model->removed,
		]);
	}
}
