<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Dispensation;

use App\Models\Dispensation;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class DispensationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Dispensation $dispensation */
		$dispensation = Dispensation::factory()->create();

		$this->assertDatabaseHas($dispensation->getTable(), [
			"id" => $dispensation->id,
			"prescription_id" => $dispensation->prescription_id,
			"user_id" => $dispensation->user->id,
			"line" => $dispensation->line,
			"issue_date" => $dispensation->issue_date->format("Y-m-d"),
			"units" => $dispensation->units,
			"qty" => $dispensation->qty,
			"note" => $dispensation->note,
			"signed" => $dispensation->signed,
			"location_id" => $dispensation->location->id,
			"on_estimate" => $dispensation->on_estimate,
		]);
	}
}
