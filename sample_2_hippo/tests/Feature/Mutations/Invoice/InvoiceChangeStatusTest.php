<?php

namespace Tests\Feature\Mutations\Invoice;

use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\In;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceChangeStatusTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	private string $query = '
			mutation InvoiceSaveDetailsMutation(
				$input: invoiceSaveDetailsInput!, 
				$locationId: Int
			) {
			invoiceSaveDetails(input: $input) {
				data {
					id
					comment
					printComment
					rounding
					isTaxable
					isActive
					total
					amountDue
					totalPayments
					createdAt
					completedAt
					totalPayments
					invoiceStatus {
						id
						name
					}
					location {
						id
						name
						streetAddress
						city
						subregion {
							name
							code
						}
						tz {
							name
							offset
							php_supported
						}
						organization {
							currencySymbol
						}
						zip
						phone1
						email
						imageName
						imageUrl
					}
					patient {
						id
						name
					}
					owner {
						id
						firstName
						lastName
						fullName
						address1
						address2
						city
						subregion {
							name
							code
						}
						zip
						phone
						email
					}
					user {
						id
						firstName
					}
					invoiceItems {
						id
						line
						quantity
						name
						number
						price
						discountPercent
						discountAmount
						total
						serialNumber
						administeredDate
						description
						allowAltDescription
						costPrice
						volumePrice
						markupPercentage
						unitPrice
						minimumSaleAmount
						dispensingFee
						isVaccine
						isPrescription
						isSerialized
						isControlledSubstance
						isEuthanasia
						isReproductive
						hideFromRegister
						requiresProvider
						isInWellnessPlan
						vcpItemId
						drugIdentifier
						belongsToKitId
						isSingleLineKit
						credit {
							number
						}
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
							remaining
							itemType {
								name
						}
						category {
							name
						}
						locationQuantity(location: $locationId)
					}
					invoiceItemTaxes {
						id
						name
						percent
						amount
						tax {
							id
						}
					}
				inventoryTransactions {
					id
					inventory {
						id
						expirationDate
					}
				}
			}
			invoicePayments {
				id
				amountApplied
				payment {
					id
					amount
					receivedAt
					paymentMethod {
						id
						name
					}
					clearentTransaction {
						cardType
						lastFourDigits
						authorizationCode
					}
				}
			}
			}
			}
			}
		';
	public function test_can_change_invoice_status_to_estimate()
	{
		$invoice = Invoice::factory()->create([
			"location_id" => 1,
		]);

		$this->assertTrue($invoice->status_id === Invoice::OPEN_STATUS);
		$this->assertTrue($invoice->location->id === 1);

		$variables = [
			"input" => [
				"comment" => null,
				"id" => $invoice->id,
				"isEstimate" => true,
				"isTaxable" => true,
				"printComment" => false,
			],
			"locationId" => 1,
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertStatus(200);
		$response
			->assertJsonStructure([
				"data" => [
					"invoiceSaveDetails" => [
						"data" => [
							"*" => [
								"id",
								"comment",
								"printComment",
								"rounding",
								"isTaxable",
								"isActive",
								"total",
								"amountDue",
								"totalPayments",
								"createdAt",
								"completedAt",
								"invoiceStatus",
								"location",
								"patient",
								"owner",
								"user",
								"invoiceItems",
								"invoicePayments",
							],
						],
					],
				],
			])
			->assertJsonFragment([
				"id" => "{$invoice->id}",
				"invoiceStatus" => [
					"id" => (string) Invoice::ESTIMATE_STATUS,
					"name" => "Estimate",
				],
			]);
	}

	public function test_can_change_invoice_status_to_open()
	{
		$invoice = Invoice::factory()->create([
			"location_id" => 1,
			"status_id" => Invoice::ESTIMATE_STATUS,
		]);

		$this->assertTrue($invoice->status_id === Invoice::ESTIMATE_STATUS);
		$this->assertTrue($invoice->location->id === 1);

		$variables = [
			"input" => [
				"comment" => null,
				"id" => $invoice->id,
				"isEstimate" => false,
				"isTaxable" => true,
				"printComment" => false,
			],
			"locationId" => 1,
		];
		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);
		$response
			->assertJsonStructure([
				"data" => [
					"invoiceSaveDetails" => [
						"data" => [
							"*" => [
								"id",
								"comment",
								"printComment",
								"rounding",
								"isTaxable",
								"isActive",
								"total",
								"amountDue",
								"totalPayments",
								"createdAt",
								"completedAt",
								"invoiceStatus",
								"location",
								"patient",
								"owner",
								"user",
								"invoiceItems",
								"invoicePayments",
							],
						],
					],
				],
			])
			->assertJsonFragment([
				"id" => "{$invoice->id}",
				"invoiceStatus" => [
					"id" => (string) Invoice::OPEN_STATUS,
					"name" => "Open",
				],
			]);
	}

	public function test_change_invoice_status_to_open_with_excessive_quantity_throws_an_exception()
	{
		$location = Location::factory()->create([
			"timezone" => 15,
		]);

		// create stocking item
		// create receiving/inventory
		$this->setupTest($location);

		// create invoice with estimate status
		$invoice = Invoice::factory()->create([
			"location_id" => $location->id,
			"status_id" => Invoice::ESTIMATE_STATUS,
		]);
		$this->assertTrue($invoice->status_id === Invoice::ESTIMATE_STATUS);

		$query =
			'{
					items(
						name: "Invoice Status Test Item"
						location: "' .
			$location->id .
			'"
					) {
						data {
							id
							name
							remaining
							createdAt
							deletedAt
							locationQuantity(location: ' .
			$location->id .
			')
					 }
					}
				}';
		$response = $this->postGraphqlJson($query)->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Invoice Status Test Item",
							"remaining" => 100,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 100,
						],
					],
				],
			],
		]);

		$itemId = $response->json("data.items.data")[0]["id"];
		$preAddToInvoiceQuantity = $response->json("data.items.data")[0][
			"locationQuantity"
		];
		$this->assertTrue($preAddToInvoiceQuantity === 100);

		// add item to invoice
		$query = 'mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
					invoiceItemCreate(input: $input) {
						data {
							item {
								locationQuantity(location: $location)
				}}}}';
		$variables = [
			"input" => [
				"administeredDate" => "June 26 2023",
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"invoice" => $invoice->id,
				"item" => $itemId,
				"provider" => 0,
				"quantity" => 101,
			],
			"location" => $location->id,
		];
		$response = $this->postGraphqlJsonWithVariables($query, $variables);
		$response->assertSee(
			"The requested quantity of 101 exceeds the remaining 100.00000 in inventory.",
		);
	}

	public function test_can_change_invoice_status_to_open_after_exception()
	{
		$location = Location::factory()->create([
			"timezone" => 15,
		]);

		// create stocking item
		// create receiving/inventory
		$this->setupTest($location);

		// create invoice with estimate status
		$invoice = Invoice::factory()->create([
			"location_id" => $location->id,
			"status_id" => Invoice::ESTIMATE_STATUS,
		]);
		$this->assertTrue($invoice->status_id === Invoice::ESTIMATE_STATUS);

		$query =
			'{
					items(
						name: "Invoice Status Test Item"
						location: "' .
			$location->id .
			'"
					) {
						data {
							id
							name
							remaining
							createdAt
							deletedAt
							locationQuantity(location: ' .
			$location->id .
			')
					 }
					}
				}';
		$response = $this->postGraphqlJson($query)->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Invoice Status Test Item",
							"remaining" => 100,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 100,
						],
					],
				],
			],
		]);

		$itemId = $response->json("data.items.data")[0]["id"];
		$preAddToInvoiceQuantity = $response->json("data.items.data")[0][
			"locationQuantity"
		];
		$this->assertTrue($preAddToInvoiceQuantity === 100);

		// add item to invoice
		$query = 'mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
					invoiceItemCreate(input: $input) {
						data {
							item {
								locationQuantity(location: $location)
				}}}}';
		$variables = [
			"input" => [
				"administeredDate" => "June 26 2023",
				"allowExcessiveQuantity" => true,
				"chart" => 0,
				"chartType" => "",
				"invoice" => $invoice->id,
				"item" => $itemId,
				"provider" => 0,
				"quantity" => 101,
			],
			"location" => $location->id,
		];
		$this->postGraphqlJsonWithVariables(
			$query,
			$variables,
			// confirm inventory is unchanged
		)->assertJsonFragment([
			"item" => [
				"locationQuantity" => $preAddToInvoiceQuantity,
			],
		]);

		// change invoice status to Open
		$query = '
      mutation InvoiceSaveDetailsMutation($input: invoiceSaveDetailsInput!, $locationId: Int) {
        invoiceSaveDetails(input: $input) {
          data {
				id
				invoiceStatus {
					id
					name
				}
				invoiceItems {
					item {
						remaining
						locationQuantity(location: $locationId)
					}
				}    
          }
        }
      }
    ';
		$variables = [
			"input" => [
				"id" => $invoice->id,
				"isEstimate" => false,
				"isTaxable" => true,
				"comment" => null,
				"printComment" => false,
			],
			"locationId" => $location->id,
		];
		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$variables,
		)->assertJsonFragment([
			"invoiceStatus" => [
				"id" => (string) Invoice::OPEN_STATUS,
				"name" => "Open",
			],
			"invoiceItems" => [
				[
					"item" => [
						"locationQuantity" => -1,
						"remaining" => -1,
					],
				],
			],
		]);
	}

	public function test_change_invoice_status_to_estimate_increases_inventory()
	{
		$location = Location::factory()->create([
			"timezone" => 15,
		]);

		// create stocking item
		// create receiving/inventory
		$this->setupTest($location);

		// create invoice with open status
		$invoice = Invoice::factory()->create([
			"location_id" => $location->id,
		]);
		$this->assertTrue($invoice->status_id === Invoice::OPEN_STATUS);

		$query =
			'{
			items(
				name: "Invoice Status Test Item"
				location: "' .
			$location->id .
			'"
			) {
				data {
					id
					name
					remaining
					createdAt
					deletedAt
					locationQuantity(location: ' .
			$location->id .
			')
			 }
	 		}
	 	}';

		$response = $this->postGraphqlJson($query)->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Invoice Status Test Item",
							"remaining" => 100,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 100,
						],
					],
				],
			],
		]);

		$itemId = $response->json("data.items.data")[0]["id"];
		$preAddToInvoiceQuantity = $response->json("data.items.data")[0][
			"locationQuantity"
		];
		$this->assertTrue($preAddToInvoiceQuantity === 100);

		// add item to invoice
		$query = 'mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
			invoiceItemCreate(input: $input) {
				data {
					item {
						locationQuantity(location: $location)
		}}}}';
		$variables = [
			"input" => [
				"administeredDate" => "June 26 2023",
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"invoice" => $invoice->id,
				"item" => $itemId,
				"provider" => 0,
				"quantity" => 1,
			],
			"location" => $location->id,
		];
		$this->postGraphqlJsonWithVariables(
			$query,
			$variables,
			// confirm inventory is reduced
		)->assertJsonFragment([
			"item" => [
				"locationQuantity" => $preAddToInvoiceQuantity - 1,
			],
		]);

		// change invoice status to Estimate
		$query = '
      mutation InvoiceSaveDetailsMutation($input: invoiceSaveDetailsInput!, $locationId: Int) {
        invoiceSaveDetails(input: $input) {
          data {
						id
						invoiceStatus {
							id
							name
						}
						invoiceItems {
							item {
								remaining
								locationQuantity(location: $locationId)
							}
						}    
          }
        }
      }
    ';
		$variables = [
			"input" => [
				"id" => $invoice->id,
				"isEstimate" => true,
				"isTaxable" => true,
				"comment" => null,
				"printComment" => false,
			],
			"locationId" => $location->id,
		];
		$this->postGraphqlJsonWithVariables(
			$query,
			$variables,
		)->assertJsonFragment([
			"invoiceStatus" => [
				"id" => (string) Invoice::ESTIMATE_STATUS,
				"name" => "Estimate",
			],
			"invoiceItems" => [
				[
					"item" => [
						"locationQuantity" => $preAddToInvoiceQuantity,
						"remaining" => $preAddToInvoiceQuantity,
					],
				],
			],
		]);
	}

	public function test_change_invoice_status_to_open_decreases_inventory()
	{
		$location = Location::factory()->create([
			"timezone" => 15,
		]);

		// create stocking item
		// create receiving/inventory
		$this->setupTest($location);

		// create invoice with estimate status
		$invoice = Invoice::factory()->create([
			"location_id" => $location->id,
			"status_id" => Invoice::ESTIMATE_STATUS,
		]);
		$this->assertTrue($invoice->status_id === Invoice::ESTIMATE_STATUS);

		$query =
			'{
					items(
						name: "Invoice Status Test Item"
						location: "' .
			$location->id .
			'"
					) {
						data {
							id
							name
							remaining
							createdAt
							deletedAt
							locationQuantity(location: ' .
			$location->id .
			')
					 }
					}
				}';
		$response = $this->postGraphqlJson($query)->assertJson([
			"data" => [
				"items" => [
					"data" => [
						[
							"id" => "1",
							"name" => "Invoice Status Test Item",
							"remaining" => 100,
							"createdAt" => "2022-05-21 12:00:00",
							"deletedAt" => null,
							"locationQuantity" => 100,
						],
					],
				],
			],
		]);

		$itemId = $response->json("data.items.data")[0]["id"];
		$preAddToInvoiceQuantity = $response->json("data.items.data")[0][
			"locationQuantity"
		];
		$this->assertTrue($preAddToInvoiceQuantity === 100);

		// add item to invoice
		$query = 'mutation InvoiceItemCreate($input: invoiceItemCreateInput!, $location: Int!) {
					invoiceItemCreate(input: $input) {
						data {
							item {
								locationQuantity(location: $location)
				}}}}';
		$variables = [
			"input" => [
				"administeredDate" => "June 26 2023",
				"allowExcessiveQuantity" => false,
				"chart" => 0,
				"chartType" => "",
				"invoice" => $invoice->id,
				"item" => $itemId,
				"provider" => 0,
				"quantity" => 1,
			],
			"location" => $location->id,
		];
		$this->postGraphqlJsonWithVariables(
			$query,
			$variables,
			// confirm inventory is unchanged
		)->assertJsonFragment([
			"item" => [
				"locationQuantity" => $preAddToInvoiceQuantity,
			],
		]);

		// change invoice status to Open
		$query = '
      mutation InvoiceSaveDetailsMutation($input: invoiceSaveDetailsInput!, $locationId: Int) {
        invoiceSaveDetails(input: $input) {
          data {
				id
				invoiceStatus {
					id
					name
				}
				invoiceItems {
					item {
						remaining
						locationQuantity(location: $locationId)
					}
				}    
          }
        }
      }
    ';
		$variables = [
			"input" => [
				"id" => $invoice->id,
				"isEstimate" => false,
				"isTaxable" => true,
				"comment" => null,
				"printComment" => false,
			],
			"locationId" => $location->id,
		];
		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$variables,
		)->assertJsonFragment([
			"invoiceStatus" => [
				"id" => (string) Invoice::OPEN_STATUS,
				"name" => "Open",
			],
			"invoiceItems" => [
				[
					"item" => [
						"locationQuantity" => $preAddToInvoiceQuantity - 1,
						"remaining" => $preAddToInvoiceQuantity - 1,
					],
				],
			],
		]);
	}

	private function setupTest($location)
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);
		$supplier = Supplier::factory()->create();
		$item = Item::factory()->create([
			"type_id" => 2,
			"name" => "Invoice Status Test Item",
		]);
		$itemLocation = ItemLocation::factory()->create([
			"item_id" => $item->id,
			"location_id" => $location->id,
		]);
		$receiving = Receiving::create([
			"location_id" => $location->id,
			"supplier_id" => $supplier->id,
			"status_id" => 1,
			"user_id" => 1,
			"active" => 1,
			"received_at" => now(),
			"comment" => "none",
			"old_receiving_id" => null,
		]);
		$receivingItem = ReceivingItem::create([
			"receiving_id" => $receiving->id,
			"item_id" => $item->id,
			"line" => 1,
			"quantity" => 100.0,
			"comment" => "Example comment",
			"cost_price" => 0.0,
			"discount_percentage" => 0.0,
			"unit_price" => 0.0,
			"old_receiving_item_id" => null,
			"created_at" => now(),
			"updated_at" => now(),
			"deleted_at" => null,
		]);
		Inventory::create([
			"item_id" => $item->id,
			"receiving_item_id" => $receivingItem->id,
			"location_id" => $location->id,
			"status_id" => 3,
			"lot_number" => null,
			"serial_number" => null,
			"expiration_date" => null,
			"starting_quantity" => 100.0,
			"remaining_quantity" => 100.0,
			"is_open" => 1,
			"opened_at" => "2022-05-21 12:00:00",
			"created_at" => "2022-05-21 12:00:00",
			"updated_at" => "2022-05-21 12:00:00",
			"deleted_at" => null,
		]);
	}
}
