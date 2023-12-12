<?php

namespace Tests\Feature\Factories\DrugAllergy;

use App\Models\DrugAllergy;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class DrugAllergyFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$allergy = DrugAllergy::factory()->create();

		$this->assertDatabaseHas("tblDrugAllergies", [
			"name" => $allergy->name,
		]);
	}
}
