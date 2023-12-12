<?php
namespace Tests\Feature\Mutations\InvoiceItem;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceItemCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_invoice_item_can_be_created()
	{
		$invoice = Invoice::factory()->create();
		$itemName = "Test Item";
		$item = Item::factory()->create([
			"name" => $itemName,
		]);
		$location = Location::factory()->create();
		Inventory::factory()->create([
			"item_id" => $item->id,
			"location_id" => $location->id,
			"starting_quantity" => 500.0,
			"remaining_quantity" => 500.0,
		]);
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = '
      mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
        invoiceItemCreate(input: $input) {
          data {
						quantity
						name
						number
						administeredDate
						provider {
							id
							firstName
							lastName
						}
						chart {
							id
							chartType
							createdAt
							updatedAt
						}
						item {
							locationQuantity(location: $location)
						}
					}
				}
			}
    ';
		$input = [
			"input" => [
				"administeredDate" => now(),
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"invoice" => (string) $invoice->id,
				"item" => (string) $item->id,
				"provider" => 0,
				"quantity" => 1,
			],
			"location" => (string) $location->id,
		];

		$this->postGraphqlJsonWithVariables($query, $input)
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"invoiceItemCreate" => [
						"data" => [
							"*" => [],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"invoiceItemCreate" => [
						"data" => [
							[
								"quantity" => 1,
								"name" => $itemName,
								"number" => (string) $item->number,
								"administeredDate" => "2022-05-21",
								"provider" => null,
								"chart" => null,
								"item" => [
									"locationQuantity" => 500,
								],
							],
						],
					],
				],
			]);
	}

	public function test_invoice_item_can_not_be_created_invalid_invoice()
	{
		$item = Item::factory()->create();
		$location = Location::factory()->create();
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = '
      mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
        invoiceItemCreate(input: $input) {
          data {
						quantity
						name
						number
						administeredDate
						provider {
							id
							firstName
							lastName
						}
						chart {
							id
							chartType
							createdAt
							updatedAt
						}
						item {
							locationQuantity(location: $location)
						}
					}
				}
			}
    ';
		$input = [
			"input" => [
				"administeredDate" => now(),
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"invoice" => -0x99999,
				"item" => (string) $item->id,
				"provider" => 0,
				"quantity" => 1,
			],
			"location" => (string) $location->id,
		];

		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"Please select a valid invoice",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_invoice_item_can_not_be_created_invalid_item()
	{
		$invoice = Invoice::factory()->create();
		$location = Location::factory()->create();
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = '
      mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
        invoiceItemCreate(input: $input) {
          data {
						quantity
						name
						number
						administeredDate
						provider {
							id
							firstName
							lastName
						}
						chart {
							id
							chartType
							createdAt
							updatedAt
						}
						item {
							locationQuantity(location: $location)
						}
					}
				}
			}
    ';
		$input = [
			"input" => [
				"administeredDate" => now(),
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"invoice" => $invoice->id,
				"item" => "-99999",
				"provider" => 0,
				"quantity" => 1,
			],
			"location" => (string) $location->id,
		];

		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"Please select a valid item to add",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
