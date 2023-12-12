<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemSpeciesRestriction;

use App\Models\ItemSpeciesRestriction;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemSpeciesRestrictionFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ItemSpeciesRestriction $model */
		$model = ItemSpeciesRestriction::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"item_id" => $model->item_id,
			"species_id" => $model->species_id,
		]);
	}
}
