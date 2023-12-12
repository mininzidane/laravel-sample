<?php

namespace Tests\Feature\Mutations\Item;

use App\Models\Item;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected const QUERY = 'mutation itemDeleteMutation($id: String){
                    itemDelete(id: $id) 
                    {
                        data {
                            id
                        }
                    }
                }';

	public function test_item_can_be_deleted()
	{
		/**
		 * To check that not only first item deletes correctly
		 * we are creating 10 items and delete a random one
		 * @var Item[] $itemsList
		 */
		$itemsList = [];

		for ($i = 0; $i < 10; $i++) {
			$itemsList[] = Item::factory()->create();
		}

		$item = $itemsList[array_rand($itemsList)];
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => $item->id,
		]);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"itemDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"itemDelete" => [
					"data" => [
						[
							"id" => (string) Item::query()->first()->id, //ItemDeleteMutation returns id of the first not deleted Item
						],
					],
				],
			],
		]);
	}

	public function test_item_cant_be_deleted_twice()
	{
		/** @var Item $item */
		$item = Item::factory()->create();

		$this->postGraphqlJsonWithVariables(self::QUERY, ["id" => $item->id]);
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => $item->id,
		]);

		$response->assertStatus(200);

		$this->assertContains(
			"Internal server error",
			$response->json("errors.*.message"),
		);
	}

	public function test_item_cant_be_deleted_wrong_id()
	{
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => 1234,
		]);

		$response->assertStatus(200);

		$this->assertContains(
			"Internal server error",
			$response->json("errors.*.message"),
		);
	}
}
