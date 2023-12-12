<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Receiving;

use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Inventory;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Supplier;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingSaveDetailsMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = '
		mutation ReceivingSaveDetailsMutation($receiving: Int, $supplier: Int) {
		    receivingSaveDetails(input: {receiving: $receiving, comment: "New Receiving Comment", supplier: $supplier}) {
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
	}

	public function test_can_save_receiving_details(): void
	{
		Supplier::factory()->create();
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receiving" => 1,
			"supplier" => 2,
		]);

		$id = $response->json("data.receivingSaveDetails.data.0.id");
		$this->assertDatabaseHas("receivings", [
			"id" => $id,
			"supplier_id" => 2,
			"comment" => "New Receiving Comment",
		]);
	}

	public function test_incorrect_receiving(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receiving" => 99999,
			"supplier" => 1,
		]);

		$this->assertContains(
			"The selected input.receiving is invalid.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_incorrect_supplier(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receiving" => 1,
			"supplier" => 99999,
		]);

		$this->assertIsArray($response->json("errors"));
	}

	public function test_receiving_missed(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receiving" => "",
			"supplier" => 1,
		]);

		$this->assertContains(
			"The input.receiving field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_receiving_incorrect_status(): void
	{
		$receiving = Receiving::find(1);
		$receiving->status_id = random_int(2, 3);
		$receiving->save();

		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"receiving" => 1,
			"supplier" => 1,
		]);

		$this->assertContains(
			HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			$response->json("errors.*.errorCode"),
		);
	}
}
