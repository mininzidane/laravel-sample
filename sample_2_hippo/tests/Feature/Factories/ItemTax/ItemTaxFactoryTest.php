<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemTax;

use App\Models\ItemTax;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemTaxFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;
	public function test_factory_can_create_data(): void
	{
		/** @var ItemTax $model */
		$model = ItemTax::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"item_id" => $model->item_id,
			"tax_id" => $model->tax_id,
		]);
	}
}
