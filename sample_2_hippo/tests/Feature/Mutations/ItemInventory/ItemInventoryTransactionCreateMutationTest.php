<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\ItemInventory;

use App\Models\Inventory;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemInventoryTransactionCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	private string $query = '
		mutation ItemInventoryTransactionCreateMutation($input: inventoryTransactionCreateInput) {
			itemInventoryTransactionCreate(input: $input) {
				data {
					id
				}
			}
		}
	';

	public function test_create_successful(): void
	{
		$now = Carbon::create(2022, 9, 27, 12);
		Carbon::setTestNow($now);
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"input" => [
				"inventoryId" => Inventory::factory()->create()->id,
				"quantity" => 2,
				"statusId" => 1,
				"isShrink" => true,
				"comment" => "some text",
				"shrinkReason" => "some shrink reason",
				"transactionAt" => Carbon::now()->toDateTimeLocalString(),
			],
		]);
		$inventoryId = $response->json(
			"data.itemInventoryTransactionCreate.data.0.id",
		);
		$this->assertDatabaseHas("inventory_transactions", [
			"id" => 1,
			"inventory_id" => $inventoryId,
			"status_id" => 1,
			"quantity" => 2,
			"is_shrink" => 1,
			"comment" => "some text",
			"shrink_reason" => "some shrink reason",
			"transaction_at" => Carbon::now()->toDateTimeLocalString(),
		]);
	}

	public function test_inventory_id_incorrect(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"input" => [
				"inventoryId" => 999,
				"quantity" => 2,
				"statusId" => 1,
				"isShrink" => true,
			],
		]);
		$this->assertIsArray($response->json("errors"));
	}
}
