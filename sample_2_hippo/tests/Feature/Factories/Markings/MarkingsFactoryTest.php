<?php

namespace Tests\Feature\Factories\Markings;

use App\Models\Markings;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class MarkingsFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Markings $model */
		$model = Markings::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"species" => $model->species,
			"name" => $model->name,
		]);

		// Check that appends attribute is set
		$this->assertEquals(0, $model->patientCount);
	}
}
