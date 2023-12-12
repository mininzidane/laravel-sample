<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\ItemInventory;

use App\Models\Item;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemInventoryNewLineCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	private string $query = '
		mutation ItemInventoryNewLineCreateMutation($itemId: Int, $locationId: Int, $statusId: Int) {
			itemInventoryNewLineCreate(input: {
				itemId: $itemId,
				locationId: $locationId,
				statusId: $statusId,
				isOpen: true
			}) {
				data {
					id,
					startingQuantity
				}
			}
		}
	';

	public function setUp(): void
	{
		parent::setUp();
		/** @var Item $item */
		Item::factory()->create();
	}

	public function test_can_create_new_line_on_stock_item(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"itemId" => 1,
			"locationId" => 1,
			"statusId" => 3,
		]);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"itemInventoryNewLineCreate" => [
						"data" => [
							"*" => ["id", "startingQuantity"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"itemInventoryNewLineCreate" => [
						"data" => [
							[
								"id" => "1",
								"startingQuantity" => 0,
							],
						],
					],
				],
			]);

		$id = $response->json("data.itemInventoryNewLineCreate.data.0.id");
		$this->assertDatabaseHas("inventory", [
			"id" => $id,
			"item_id" => 1,
			"location_id" => 1,
			"status_id" => 3,
			"is_open" => 1,
		]);
	}

	public function test_incorrect_item_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"itemId" => 2,
			"locationId" => 1,
			"statusId" => 3,
		]);
		$this->assertContains(
			"The selected input.item id is invalid.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_incorrect_location_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"itemId" => 1,
			"locationId" => 2,
			"statusId" => 3,
		]);
		$this->assertContains(
			"The selected input.location id is invalid.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
