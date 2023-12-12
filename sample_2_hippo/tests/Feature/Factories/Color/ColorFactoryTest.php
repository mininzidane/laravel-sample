<?php

namespace Tests\Feature\Factories\Color;

use App\Models\Color;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class ColorFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$color = Color::factory()->create();

		$this->assertDatabaseHas("tblColors", [
			"id" => $color->id,
			"species" => $color->species,
			"name" => $color->name,
		]);

		// Check that appends attribute is set
		$this->assertEquals(0, $color->patient_count);
	}
}
