<?php
namespace Tests\Feature\Query\ClearentTerminal;

use App\Models\ClearentTerminal;
use App\Models\Location;
use App\Models\PaymentPlatform;
use Database\Factories\ClearentTerminalFactory;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ClearentTerminalQueryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_clearent_terminals_only_list_for_location1()
	{
		$this->setUpFactories();

		$query =
			'{
			clearentTerminals (limit: 100, page: 1, locations: "' .
			$this->location1->id .
			'") {
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
			}}
			';
		$this->postGraphqlJson($query)
			->assertStatus(200)
			->assertJsonCount(1)
			->assertExactJson([
				"data" => [
					"clearentTerminals" => [
						"data" => [
							[
								"id" => (string) $this->terminal1->id,
								"terminalId" => $this->terminal1->terminal_id,
								"name" => $this->terminal1->name,
								"apiKey" => $this->terminal1->api_key,
								"paymentPlatform" => [
									"id" =>
										(string) $this->terminal1
											->paymentPlatform->id,
									"name" =>
										$this->terminal1->paymentPlatform->name,
								],
								"location" => [
									"id" => (string) $this->location1->id,
									"name" => $this->location1->name,
								],
								"clearentTransactions" => [],
							],
						],
					],
				],
			]);
	}

	public function test_clearent_terminals_only_list_for_location2()
	{
		$this->setUpFactories();

		$query =
			'{
			clearentTerminals (limit: 100, page: 1, locations: "' .
			$this->location2->id .
			'") {
				data {
					id
				}
			}}
			';
		$this->postGraphqlJson($query)
			->assertExactJson([
				"data" => [
					"clearentTerminals" => [
						"data" => [
							[
								"id" => (string) $this->terminal2->id,
							],
						],
					],
				],
			])
			->assertStatus(200)
			->assertJsonCount(1);
	}

	public function test_clearent_terminals_all_list_for_no_location()
	{
		$this->setUpFactories();

		$query = '{
			clearentTerminals (limit: 100, page: 1) {
				data {
					id
				}
			}}
			';
		$this->postGraphqlJson($query)
			->assertStatus(200)
			->assertExactJson([
				"data" => [
					"clearentTerminals" => [
						"data" => [
							[
								"id" => (string) $this->terminal1->id,
							],
							[
								"id" => (string) $this->terminal2->id,
							],
						],
					],
				],
			]);
	}

	private function setUpFactories()
	{
		$this->location1 = Location::factory()->create();
		$this->location2 = Location::factory()->create();
		$this->paymentPlatform = PaymentPlatform::factory()->create();

		$this->terminal1 = ClearentTerminal::factory()->create([
			"location_id" => $this->location1->id,
			"payment_platform_id" => $this->paymentPlatform->id,
		]);
		$this->terminal2 = ClearentTerminal::factory()->create([
			"location_id" => $this->location2->id,
		]);
	}
}
