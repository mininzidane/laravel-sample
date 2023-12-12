<?php
namespace Tests\Feature\Query\Item;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemLocationQuantityTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_if_location_one_quantity_returns_correct_value()
	{
		$location = Location::factory()->create();
		$location2 = Location::factory()->create();

		$this->setupTest($location, $location2);

		$query =
			'{
             items(
                name: "Zeniquin 100mg tablets"
                location: "' .
			$location->id .
			'"
            ) {
                total
                per_page
                current_page
                from
                to
                last_page
                has_more_pages
                data {
                    id
                    name
                    remaining
                    createdAt
                    deletedAt
                    locationQuantity(location: ' .
			$location->id .
			')
                }
            }
        }';

		$response1 = $this->postGraphqlJson($query);

		$response1->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Zeniquin 100mg tablets",
							"remaining" => 666,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 555,
						],
					],
				],
			],
		]);
	}

	public function test_if_location_two_quantity_returns_correct_value()
	{
		$location = Location::factory()->create();
		$location2 = Location::factory()->create();

		$this->setupTest($location, $location2);

		$query =
			'{
             items(
                name: "Zeniquin 100mg tablets"
                location: "' .
			$location2->id .
			'"
            ) {
                total
                per_page
                current_page
                from
                to
                last_page
                has_more_pages
                data {
                    id
                    name
                    remaining
                    createdAt
                    deletedAt
                    locationQuantity(location: ' .
			$location2->id .
			')
                }
            }
        }';

		$response1 = $this->postGraphqlJson($query);

		$response1->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Zeniquin 100mg tablets",
							"remaining" => 666,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 111,
						],
					],
				],
			],
		]);
	}

	public function test_if_location_argument_not_set_return_remaining_quantity()
	{
		$location = Location::factory()->create();
		$location2 = Location::factory()->create();

		$this->setupTest($location, $location2);

		$query =
			'{
             items(
                name: "Zeniquin 100mg tablets"
                location: "' .
			$location2->id .
			'"
            ) {
                total
                per_page
                current_page
                from
                to
                last_page
                has_more_pages
                data {
                    id
                    name
                    remaining
                    createdAt
                    deletedAt
                    locationQuantity
                }
            }
        }';

		$response1 = $this->postGraphqlJson($query);

		$response1->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Zeniquin 100mg tablets",
							"remaining" => 666,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 0,
						],
					],
				],
			],
		]);
	}

	private function setupTest($location, $location2)
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$supplier = Supplier::factory()->create();

		$item = Item::factory()->create([
			"type_id" => 2,
			"name" => "Zeniquin 100mg tablets",
		]);

		$itemLocation = ItemLocation::factory()->create([
			"item_id" => $item->id,
			"location_id" => $location->id,
		]);
		$itemLocation2 = ItemLocation::factory()->create([
			"item_id" => $item->id,
			"location_id" => $location2->id,
		]);

		$recieving = Receiving::create([
			"location_id" => $location->id,
			"supplier_id" => $supplier->id,
			"status_id" => 1,
			"user_id" => 1,
			"active" => 1,
			"received_at" => now(),
			"comment" => "none",
			"old_receiving_id" => null,
		]);
		$recieving2 = Receiving::create([
			"location_id" => $location2->id,
			"supplier_id" => $supplier->id,
			"status_id" => 1,
			"user_id" => 1,
			"active" => 1,
			"received_at" => now(),
			"comment" => "none",
			"old_receiving_id" => null,
		]);

		$recievingItem = ReceivingItem::create([
			"receiving_id" => $recieving->id,
			"item_id" => $item->id,
			"line" => 1,
			"quantity" => 1.0,
			"comment" => "Example comment",
			"cost_price" => 0.0,
			"discount_percentage" => 0.0,
			"unit_price" => 0.0,
			"old_receiving_item_id" => null,
			"created_at" => now(),
			"updated_at" => now(),
			"deleted_at" => null,
		]);
		$recievingItem2 = ReceivingItem::create([
			"receiving_id" => $recieving2->id,
			"item_id" => $item->id,
			"line" => 1,
			"quantity" => 1.0,
			"comment" => "Example comment",
			"cost_price" => 0.0,
			"discount_percentage" => 0.0,
			"unit_price" => 0.0,
			"old_receiving_item_id" => null,
			"created_at" => now(),
			"updated_at" => now(),
			"deleted_at" => null,
		]);

		Inventory::create([
			"item_id" => $item->id,
			"receiving_item_id" => $recievingItem->id,
			"location_id" => $location->id,
			"status_id" => 3,
			"lot_number" => null,
			"serial_number" => null,
			"expiration_date" => null,
			"starting_quantity" => 777.0,
			"remaining_quantity" => 555.0,
			"is_open" => 0,
			"opened_at" => null,
			"created_at" => "2022-05-21 12:00:00",
			"updated_at" => "2022-05-21 12:00:00",
			"deleted_at" => null,
		]);
		Inventory::create([
			"item_id" => $item->id,
			"receiving_item_id" => $recievingItem2->id,
			"location_id" => $location2->id,
			"status_id" => 3,
			"lot_number" => null,
			"serial_number" => null,
			"expiration_date" => null,
			"starting_quantity" => 888.0,
			"remaining_quantity" => 111.0,
			"is_open" => 0,
			"opened_at" => null,
			"created_at" => "2022-05-21 12:00:00",
			"updated_at" => "2022-05-21 12:00:00",
			"deleted_at" => null,
		]);
	}
}
