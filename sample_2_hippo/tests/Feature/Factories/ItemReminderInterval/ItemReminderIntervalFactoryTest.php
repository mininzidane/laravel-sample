<?php

namespace Tests\Feature\Factories\ItemReminderInterval;

use App\Models\ItemReminderInterval;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemReminderIntervalFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	/** @test
	 * Adding some comments because my push is not working
	 * */
	public function it_creates_item_reminder_interval_using_factory()
	{
		$itemReminderIntervals = ItemReminderInterval::factory()
			->count(3)
			->create();

		$this->assertCount(3, ItemReminderInterval::all());

		// Go through each created item and verify it in the database
		foreach ($itemReminderIntervals as $itemReminderInterval) {
			$this->assertDatabaseHas("item_reminder_intervals", [
				"item_id" => $itemReminderInterval->item_id,
				"reminder_interval_id" =>
					$itemReminderInterval->reminder_interval_id,
				"is_default" => $itemReminderInterval->is_default,
			]);
		}
	}
}
