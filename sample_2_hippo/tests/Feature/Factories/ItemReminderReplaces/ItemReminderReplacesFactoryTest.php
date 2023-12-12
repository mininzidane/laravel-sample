<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\ItemReminderReplaces;

use App\Models\ItemReminderReplaces;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemReminderReplacesFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;
	public function test_factory_can_create_data(): void
	{
		/** @var ItemReminderReplaces $model */
		$model = ItemReminderReplaces::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"item_id" => $model->item_id,
			"replaces_item_id" => $model->replaces_item_id,
		]);
	}
}
