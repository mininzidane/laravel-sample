<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\PatientMarkings;

use App\Models\PatientMarkings;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientMarkingsFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PatientMarkings $model */
		$model = PatientMarkings::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"markings" => $model->markings,
		]);
	}
}
