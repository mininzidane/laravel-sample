<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemReplaces;

use App\Models\ItemReplaces;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemReplacesFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var ItemReplaces $model */
		$model = ItemReplaces::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"item_id" => $model->item_id,
			"replaces_item_id" => $model->replaces_item_id,
		]);
	}
}
