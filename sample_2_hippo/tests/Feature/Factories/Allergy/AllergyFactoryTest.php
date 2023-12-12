<?php

namespace Tests\Feature\Factories\Allergy;

use App\Models\Allergy;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class AllergyFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$allergy = Allergy::factory()->create();

		$this->assertDatabaseHas("tblAllergies", [
			"name" => $allergy->name,
		]);
	}
}
