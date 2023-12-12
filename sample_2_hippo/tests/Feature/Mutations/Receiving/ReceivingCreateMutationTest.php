<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Receiving;

use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ReceivingCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = '
		mutation ReceivingCreateMutation($location: Int) {
		    receivingCreate(input: {id: "", comment: "New Receiving", location: $location}) {
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

	public function test_receiving_can_be_created(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"location" => 1,
		]);
		$receivingId = $response->json("data.receivingCreate.data.0.id");

		$this->assertDatabaseHas("receivings", [
			"id" => $receivingId,
			"status_id" => 1,
			"comment" => "New Receiving",
		]);
	}

	public function test_receiving_incorrect_location(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => "",
			"location" => 999999,
		]);
		$this->assertContains(
			"Please select a valid location",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_receiving_location_required(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, []);
		$this->assertContains(
			"A location must be provided for this request",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
