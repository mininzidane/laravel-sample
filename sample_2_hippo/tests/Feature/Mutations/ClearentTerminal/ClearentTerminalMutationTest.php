<?php

namespace Tests\Feature\Mutations\ClearentTerminal;

use App\Models\ClearentTerminal;
use App\Models\Location;
use App\Models\PaymentPlatform;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ClearentTerminalMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_can_update_clearent_terminal()
	{
		$paymentPlatform = PaymentPlatform::factory()->create();
		$location1 = Location::factory()->create();
		$location2 = Location::factory()->create();
		$clearentTerminal = ClearentTerminal::factory()->create([
			"payment_platform_id" => $paymentPlatform->id,
			"location_id" => $location1->id,
		]);
		$newTerminalName = "Über Terminal 3000";

		$query =
			'
    mutation {
      clearentTerminalUpdate(input: {
      	id: ' .
			$clearentTerminal->id .
			', 
      	location: ' .
			$location2->id .
			', 
      	name: "' .
			$newTerminalName .
			'"
      	}) { 
				data {
					id
					terminalId
					name
					apiKey
					paymentPlatform {
						id
						name
					}
					location {
						id
						name
					}
					clearentTransactions {
						id
						requestId
						requestType
						requestBody
						responseStatus
						responseBody
						platformMode
					}
				}
        }
    }';

		$this->postGraphqlJson($query)
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"clearentTerminalUpdate" => [
						"data" => [
							"*" => [
								"id",
								"terminalId",
								"name",
								"apiKey",
								"paymentPlatform" => ["id", "name"],
								"location" => ["id", "name"],
								"clearentTransactions",
							],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"clearentTerminalUpdate" => [
						"data" => [
							[
								"id" => "{$clearentTerminal->id}",
								"terminalId" => "{$clearentTerminal->terminal_id}",
								"name" => "$newTerminalName",
								"apiKey" => "{$clearentTerminal->api_key}",
								"paymentPlatform" => [
									"id" => "{$paymentPlatform->id}",
									"name" => "{$paymentPlatform->name}",
								],
								"location" => [
									"id" => "{$location2->id}",
									"name" => "{$location2->name}",
								],
								"clearentTransactions" => [],
							],
						],
					],
				],
			]);
	}

	public function test_can_not_update_without_id()
	{
		$location = Location::factory()->create();
		$newTerminalName = "Über Terminal 3000";

		$query =
			'
    mutation {
      clearentTerminalUpdate(input: {
      	location: ' .
			$location->id .
			', 
      	name: "' .
			$newTerminalName .
			'"
      	}) { 
				data {
					id
					}
        }
    }';

		$response = $this->postGraphqlJson($query);
		$response->assertStatus(200);
		$this->assertContains(
			"Please select an Clearent Terminal to update",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_can_not_update_with_invalid_id()
	{
		$location = Location::factory()->create();
		$newTerminalName = "Über Terminal 3000";

		$query =
			'
    mutation {
      clearentTerminalUpdate(input: {
      id: 13,
      	location: ' .
			$location->id .
			', 
      	name: "' .
			$newTerminalName .
			'"
      	}) { 
				data {
					id
					}
        }
    }';

		$response = $this->postGraphqlJson($query);
		$response->assertStatus(200);
		$this->assertContains(
			"The specified clearent terminal does not exist",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_can_not_update_with_invalid_location()
	{
		$location = Location::factory()->create([
			"id" => 100,
		]);
		$clearentTerminal = ClearentTerminal::factory()->create([
			"location_id" => $location->id,
		]);
		$newTerminalName = "Über Terminal 3000";

		$query =
			'
    mutation {
      clearentTerminalUpdate(input: {
      	id: ' .
			$clearentTerminal->id .
			',
      	location: 13
      	name: "' .
			$newTerminalName .
			'"
      	}) { 
				data {
					id
					}
        }
    }';

		$response = $this->postGraphqlJson($query);
		$response->assertStatus(200);
		$this->assertContains(
			"The specified location does not exist",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_can_not_update_without_location()
	{
		$clearentTerminal = ClearentTerminal::factory()->create();
		$newTerminalName = "Über Terminal 3000";

		$query =
			'
    mutation {
      clearentTerminalUpdate(input: {
      	id: ' .
			$clearentTerminal->id .
			',
      	name: "' .
			$newTerminalName .
			'"
      	}) { 
				data {
					id
					}
        }
    }';

		$response = $this->postGraphqlJson($query);
		$response->assertStatus(200);
		$this->assertContains(
			"Please select a location for this item",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
