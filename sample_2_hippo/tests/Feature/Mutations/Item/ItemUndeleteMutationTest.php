<?php

namespace Tests\Feature\Mutations\Item;

use App\Models\Item;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemUndeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected const QUERY = 'mutation itemUndeleteMutation($itemIds: [ID]){
                    itemUndelete(itemIds: $itemIds) 
                    {
                        data {
                            id
                        }
                    }
                }';

	public function test_item_can_be_undeleted()
	{
		/** @var Item $item */
		$item = Item::factory()->create(["deleted_at" => now()]);

		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"itemIds" => [$item->id],
		]);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"itemUndelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"itemUndelete" => [
					"data" => [
						[
							"id" => (string) $item->id,
						],
					],
				],
			],
		]);
	}

	public function test_not_deleted_item_can_be_undeleted()
	{
		/** @var Item $item */
		$item = Item::factory()->create();

		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"itemIds" => [$item->id],
		]);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"itemUndelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"itemUndelete" => [
					"data" => [
						[
							"id" => (string) $item->id,
						],
					],
				],
			],
		]);
	}
}
