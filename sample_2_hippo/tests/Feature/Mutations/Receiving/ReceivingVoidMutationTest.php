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

class ReceivingVoidMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = '
	    mutation ReceivingVoidMutation($id: String!) {
	      receivingVoid(input: {id: $id}) {
	        data {
	          id
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

	public function test_void_successful(): void
	{
		$id = 1;
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => $id,
		]);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"receivingVoid" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$this->assertDatabaseHas("receivings", [
			"id" => $id,
			"active" => 0,
			"deleted_at" => "2022-05-21 12:00:00",
		]);

		$receiving = Receiving::withTrashed()->find($id);
		self::assertSame(0, $receiving->active);
		self::assertSame(3, $receiving->status_id);
		self::assertNotNull($receiving->deleted_at);

		/** @var Inventory $inventory */
		$inventory = Inventory::withTrashed()->first();
		self::assertSame(2, $inventory->status_id);
		self::assertNotNull($inventory->deleted_at);
	}

	public function test_incorrect_status(): void
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
}
