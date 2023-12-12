<?php

namespace Tests\Feature\Mutations\ReceivingItem;

use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingItemMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_receiving_item_can_be_updated()
	{
		$this->create_supplier();
		$this->create_item(2);
		$this->create_receiving();

		$query = '
            mutation {
                receivingItemCreate(input: {quantity: 55, item: 1, receiving: 1}) {
                    data {
            
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
                        }
                        inventory {
                            id
                            lotNumber
                            serialNumber
                            expirationDate
                        }
            
                    }
                }
            }
            ';

		$response = $this->postGraphqlJson($query);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"receivingItemCreate" => [
					"data" => [
						"*" => ["id", "quantity"],
					],
				],
			],
		]);
		$this->assertDatabaseHas("receiving_items", [
			"id" => 1,
			"quantity" => 55,
		]);
	}

	public function test_receiving_can_be_updated()
	{
		$this->create_supplier();
		$this->create_item(2);
		$this->create_receiving();
		$this->update_by_create_receiving();

		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = '
                mutation {
                    receivingItemUpdate(input: {id: 1, quantity: 66, costPrice: 0.3, serialNumber: "34"}) {
                        data {
                            id
                            line
                            quantity
                            comment
                            costPrice      
                        }
                    }
                }
            ';

		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"receivingItemUpdate" => [
					"data" => [
						"*" => [
							"id",
							"line",
							"quantity",
							"comment",
							"costPrice",
						],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"receivingItemUpdate" => [
					"data" => [
						[
							"id" => "1",
							"line" => 1,
							"quantity" => 66,
							"comment" => "",
							"costPrice" => 0.3,
						],
					],
				],
			],
		]);
		$this->assertDatabaseHas("inventory", [
			"id" => 1,
			"starting_quantity" => 66,
		]);
	}
}
