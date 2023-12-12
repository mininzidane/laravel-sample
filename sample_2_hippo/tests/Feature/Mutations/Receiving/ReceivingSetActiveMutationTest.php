<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Receiving;

use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Inventory;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingSetActiveMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = '
	  mutation ReceivingSetActive($receivingId: String!) {
	    receivingSetActive(input: {receivingId: $receivingId}) {
	      data {
	        id
	        comment
	        active
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
		Receiving::factory()->create();
	}

	public function test_receiving_can_be_set_active(): void
	{
		$receiving = Receiving::find(1);
		$receiving->active = 0;
		$receiving->save();

		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receivingId" => 1,
		]);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"receivingSetActive" => [
					"data" => [
						"*" => ["id", "comment", "active"],
					],
				],
			],
		]);

		$id = $response->json("data.receivingSetActive.data.0.id");
		$this->assertDatabaseHas("receivings", [
			"id" => $id,
			"active" => 1,
		]);
		$this->assertDatabaseHas("receivings", [
			"id" => 2,
			"active" => 0,
		]);
	}

	public function test_receiving_incorrect_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receivingId" => 99999,
		]);

		$this->assertContains(
			"The specified receiving does not exist",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_incorrect_receiving_status(): void
	{
		$receiving = Receiving::find(1);
		$receiving->status_id = random_int(2, 3);
		$receiving->save();

		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receivingId" => 1,
		]);

		$this->assertContains(
			HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			$response->json("errors.*.errorCode"),
		);
	}
}
