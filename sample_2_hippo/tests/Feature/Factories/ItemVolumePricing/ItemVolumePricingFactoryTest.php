<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemVolumePricing;

use App\Models\ItemVolumePricing;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemVolumePricingFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ItemVolumePricing $model */
		$model = ItemVolumePricing::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"item_id" => $model->item_id,
			"quantity" => $model->quantity,
			"unit_price" => $model->unit_price,
		]);
	}
}
