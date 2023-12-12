<?php

namespace Tests\Feature\Factories\Breed;

use App\Models\Breed;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class BreedFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$breed = Breed::factory()->create();

		$this->assertDatabaseHas("tblBreeds", [
			"id" => $breed->id,
			"species" => $breed->species,
			"name" => $breed->name,
		]);

		// Check that appends attribute is set
		$this->assertEquals(0, $breed->patient_count);
	}
}
