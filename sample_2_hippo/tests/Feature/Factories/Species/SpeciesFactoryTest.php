<?php

namespace Tests\Feature\Factories\Species;

use App\Models\Species;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class SpeciesFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$species = Species::factory()->create();

		$this->assertDatabaseHas("tblSpecies", [
			"id" => $species->id,
			"name" => $species->name,
		]);
	}
}
