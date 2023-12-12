<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Receiving;

use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Inventory;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingCompleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = '
      mutation ReceivingCompleteMutation($id: String!){
        receivingComplete(input: {id: $id}) {
          data {
            id
            receivedAt
            createdAt
            updatedAt
            comment
            location {
              id
              name
            }
            supplier {
              id
              companyName
            }
            user {
              id
              firstName
              lastName
            }
            receivingItems {
              id
              line
              quantity
              comment
              costPrice
              discountPercentage
              item {
                name
                description
                unitPrice
                number
                costPrice
                remaining
              }
              inventory {
                id
                lotNumber
                serialNumber
                expirationDate
              }
            }
            receivingStatus {
              name
            }
            items {
              id
              name
            }
          }
        }
      }
    ';

	public function setUp(): void
	{
		parent::setUp();
		/** @var ReceivingItem $receivingItem */
		$receivingItem = ReceivingItem::factory()->create();
		Inventory::factory()->create([
			"receiving_item_id" => $receivingItem->id,
		]);
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);
	}

	public function test_receiving_can_be_completed(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => 1,
		]);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"receivingComplete" => [
					"data" => [
						"*" => ["id", "receivedAt"],
					],
				],
			],
		]);

		$id = $response->json("data.receivingComplete.data.0.id");
		$receiving = Receiving::find($id);
		/** @var ReceivingItem $receivingItem */
		$receivingItem = $receiving->receivingItems()->first();
		$item = $receivingItem->item;
		if ($receivingItem->cost_price > $item->cost_price) {
			self::assertSame(
				round(
					$item->cost_price * (1 + $item->markup_percentage / 100),
					2,
				),
				$item->unit_price,
			);
			self::assertSame($item->cost_price, $receivingItem->cost_price);
		}
		self::assertGreaterThan(0, $item->markup_percentage);

		$this->assertDatabaseHas("receivings", [
			"id" => $id,
			"status_id" => 2,
			"received_at" => Carbon::now(),
			"active" => 0,
		]);
		$this->assertDatabaseHas("inventory_transactions", [
			"inventory_id" => $receivingItem->inventory[0]->id,
			"user_id" => $receiving->user->id,
			"status_id" => 3,
			"quantity" => $receivingItem->inventory[0]->starting_quantity,
			"comment" => "Initial Receiving",
		]);
	}

	public function test_receiving_incorrect_status(): void
	{
		$receiving = Receiving::find(1);
		$receiving->status_id = random_int(2, 3);
		$receiving->save();

		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => 1,
		]);

		$this->assertContains(
			HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			$response->json("errors.*.errorCode"),
		);
	}

	public function test_receiving_incorrect_supplier(): void
	{
		$receiving = Receiving::find(1);
		$receiving->supplier_id = null;
		$receiving->save();

		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => 1,
		]);

		$this->assertContains(
			HippoGraphQLErrorCodes::RECEIVING_COMPLETE_NO_SUPPLIER,
			$response->json("errors.*.errorCode"),
		);
	}
}
