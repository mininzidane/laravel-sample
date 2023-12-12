<?php

namespace Tests\Feature\Factories\ItemReminderInterval;

use App\Models\Item;
use App\Models\ReminderInterval;
use App\Models\ItemReminderInterval;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemReminderIntervalModelTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	/** @test
	 * Adding some comments because my push is not working
	 * */
	public function test_can_create_item_reminder_interval()
	{
		$item = Item::factory()->create();
		$reminderInterval = ReminderInterval::inRandomOrder()->first();

		$itemReminderInterval = ItemReminderInterval::create([
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
			"is_default" => 1,
		]);

		$this->assertDatabaseHas("item_reminder_intervals", [
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
			"is_default" => 1,
		]);
	}

	public function test_can_add_multiple_reminders_to_one_item()
	{
		$item = Item::factory()->create();

		$reminderIntervals = ReminderInterval::inRandomOrder()
			->take(3)
			->get();

		foreach ($reminderIntervals as $index => $reminderInterval) {
			$isDefault = $index === 0 ? 1 : 0; // Setting the first one as default

			ItemReminderInterval::create([
				"item_id" => $item->id,
				"reminder_interval_id" => $reminderInterval->id,
				"is_default" => $isDefault,
			]);

			$this->assertDatabaseHas("item_reminder_intervals", [
				"item_id" => $item->id,
				"reminder_interval_id" => $reminderInterval->id,
				"is_default" => $isDefault,
			]);
		}
	}

	public function test_item_relationship()
	{
		$item = Item::factory()->create();
		$reminderInterval = ReminderInterval::inRandomOrder()->first();

		$itemReminderInterval = ItemReminderInterval::create([
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
			"is_default" => 1,
		]);

		$this->assertEquals($itemReminderInterval->item->id, $item->id);
	}

	public function test_reminder_interval_relationship()
	{
		$item = Item::factory()->create();
		$reminderInterval = ReminderInterval::inRandomOrder()->first();

		$itemReminderInterval = ItemReminderInterval::create([
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
			"is_default" => 1,
		]);

		$this->assertEquals(
			$itemReminderInterval->reminderInterval->id,
			$reminderInterval->id,
		);
	}

	public function test_soft_deletes()
	{
		$item = Item::factory()->create();

		// Get a random ReminderInterval from the database.
		$reminderInterval = ReminderInterval::inRandomOrder()->first();

		$itemReminderInterval = ItemReminderInterval::create([
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
			"is_default" => 1,
		]);

		$itemReminderInterval->delete();

		$this->assertSoftDeleted("item_reminder_intervals", [
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
		]);

		$itemReminderInterval->restore();

		$this->assertDatabaseHas("item_reminder_intervals", [
			"item_id" => $item->id,
			"reminder_interval_id" => $reminderInterval->id,
		]);
	}
}
