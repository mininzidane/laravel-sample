<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\ItemInventory;

use App\Models\Inventory;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemInventoryIsOpenUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	private string $query = '
		mutation ItemInventoryIsOpenUpdateMutation($id: Int, $isOpen: Boolean) {
			itemInventoryIsOpenUpdate(id: $id, isOpen: $isOpen) {
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
		Inventory::factory()->create();
	}

	public function test_update_inventory_open_successful(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => 1,
			"isOpen" => true,
		]);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"itemInventoryIsOpenUpdate" => [
					"data" => [
						"*" => ["id", "startingQuantity"],
					],
				],
			],
		]);

		$inventoryId = $response->json(
			"data.itemInventoryIsOpenUpdate.data.0.id",
		);
		$this->assertDatabaseHas("inventory", [
			"id" => $inventoryId,
			"is_open" => 1,
		]);
	}

	public function test_incorrect_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => 188,
			"isOpen" => true,
		]);
		$this->assertContains(
			"Cannot update non-existent item inventory: 188",
			$response->json("*.*.errorMessage"),
		);
	}

	public function test_update_inventory_close_successful(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => 1,
			"isOpen" => false,
		]);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"itemInventoryIsOpenUpdate" => [
					"data" => [
						"*" => ["id", "startingQuantity"],
					],
				],
			],
		]);

		$inventoryId = $response->json(
			"data.itemInventoryIsOpenUpdate.data.0.id",
		);
		$this->assertDatabaseHas("inventory", [
			"id" => $inventoryId,
			"is_open" => 0,
		]);
	}
}
