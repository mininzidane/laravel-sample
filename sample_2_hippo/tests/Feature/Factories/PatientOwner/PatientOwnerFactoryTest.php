<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\PatientOwner;

use App\Models\PatientOwner;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class PatientOwnerFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var PatientOwner $model */
		$model = PatientOwner::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"organization_id" => $model->organization_id,
			"client_id" => $model->client_id,
			"owner_id" => $model->owner_id,
			"primary" => $model->primary,
			"percent" => $model->percent,
			"relationship_type" => $model->relationship_type,
		]);
	}
}
