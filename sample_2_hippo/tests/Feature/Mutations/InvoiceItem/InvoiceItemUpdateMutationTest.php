<?php
namespace Tests\Feature\Mutations\InvoiceItem;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceItemUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_invoice_item_can_be_updated()
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
		$provider = User::factory()->create();

		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$invoiceItem = InvoiceItem::factory()->create([
			"provider_id" => $provider->id,
		]);

		$query = '
      mutation InvoiceItemUpdate($input: invoiceItemUpdateInput!, $location: Int!) {
        invoiceItemUpdate(input: $input) {
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
				"id" => $invoiceItem->id,
				"administeredDate" => now(),
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",

				"provider" => $invoiceItem->provider_id,
				"quantity" => (float) $invoiceItem->quantity,
			],
			"location" => (string) $location->id,
		];

		$this->postGraphqlJsonWithVariables($query, $input)
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"invoiceItemUpdate" => [
						"data" => [
							"*" => [],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"invoiceItemUpdate" => [
						"data" => [
							[
								"quantity" => (int) $invoiceItem->quantity,
								"name" => $invoiceItem->name,
								"number" => (string) $invoiceItem->number,
								"administeredDate" => "2022-05-21",
								"provider" => [
									"id" => (string) $invoiceItem->provider->id,
									"firstName" => $provider->first_name,
									"lastName" => $provider->last_name,
								],
								"chart" => null,
								"item" => [
									"locationQuantity" => 0,
								],
							],
						],
					],
				],
			]);
	}

	public function test_invoice_item_can_not_be_updated_null_id()
	{
		$location = Location::factory()->create();
		$query = '
      mutation InvoiceItemUpdate($input: invoiceItemUpdateInput!, $location: Int!) {
        invoiceItemUpdate(input: $input) {
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
				"id" => null,
				"administeredDate" => null,
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"provider" => null,
				"quantity" => null,
			],
			"location" => $location->id,
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200);

		$this->assertContains(
			"Please select an Invoice Item to update",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_invoice_item_can_not_be_updated_invalid_id()
	{
		$location = Location::factory()->create();
		$query = '
      mutation InvoiceItemUpdate($input: invoiceItemUpdateInput!, $location: Int!) {
        invoiceItemUpdate(input: $input) {
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
				"id" => -99999,
				"administeredDate" => null,
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"provider" => null,
				"quantity" => null,
			],
			"location" => $location->id,
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200);

		$this->assertContains(
			"The specified invoice item does not exist",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
