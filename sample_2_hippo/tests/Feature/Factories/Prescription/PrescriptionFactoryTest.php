<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Prescription;

use App\Models\Prescription;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PrescriptionFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Prescription $prescription */
		$prescription = Prescription::factory()->create();

		$this->assertDatabaseHas($prescription->getTable(), [
			"id" => $prescription->id,
			"item_id" => $prescription->item->id,
			"client_id" => $prescription->patient->id,
			"refills_left" => $prescription->refills_left,
			"refills_original" => $prescription->refills_original,
			"user_id" => $prescription->user->id,
			"acute" => $prescription->acute,
			"chart_note" => $prescription->chart_note,
			"organization_id" => $prescription->organization->id,
			"location_id" => $prescription->location->id,
		]);
	}
}
