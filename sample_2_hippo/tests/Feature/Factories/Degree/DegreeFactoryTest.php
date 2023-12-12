<?php

namespace Tests\Feature\Factories\Degree;

use App\Models\Degree;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class DegreeFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$degree = Degree::factory()->create();

		$this->assertDatabaseHas("tblDegrees", [
			"id" => $degree->id,
			"degree" => $degree->degree,
			"doctoral" => $degree->doctoral,
		]);
	}
}
