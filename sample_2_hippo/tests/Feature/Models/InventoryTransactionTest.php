<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Location;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InventoryTransactionTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_receiving_item_create_unit_price_at_time(): void
	{
		/** @var Receiving $receiving */
		$receiving = Receiving::factory()->create();
		Item::factory()->create();
		$response = $this->update_by_create_receiving();
		$responseData = json_decode($response->baseResponse->content(), true);
		$inventoryId = (int) \Arr::get(
			$responseData,
			"data.receivingItemCreate.data.0.inventory.0.id",
		);
		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create([
			"inventory_id" => $inventoryId,
		]);
		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		$receivingItem = $receiving->receivingItems->first();

		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$receivingItem->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Receiving ID: {$receivingItem->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_receiving_complete_unit_price_at_time(): void
	{
		/** @var Receiving $receiving */
		$receiving = Receiving::factory()->create();
		/** @var ReceivingItem $receivingItem */
		$receivingItem = ReceivingItem::factory()->create([
			"receiving_id" => $receiving->id,
		]);
		/** @var Inventory $inventory */
		$inventory = Inventory::factory()->create([
			"receiving_item_id" => $receivingItem->id,
		]);
		$this->complete_receiving();

		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create([
			"inventory_id" => $inventory->id,
		]);
		$unitPriceData = $inventoryTransaction->getUnitPriceData();

		$receivingItem = $receiving->receivingItems->first();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$receivingItem->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Receiving ID: {$receivingItem->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_receiving_save_details_unit_price_at_time(): void
	{
		/** @var Receiving $receiving */
		$receiving = Receiving::factory()->create();
		/** @var ReceivingItem $receivingItem */
		$receivingItem = ReceivingItem::factory()->create([
			"receiving_id" => $receiving->id,
		]);
		/** @var Inventory $inventory */
		$inventory = Inventory::factory()->create([
			"receiving_item_id" => $receivingItem->id,
		]);
		$this->save_details_receiving();
		$receivingItem->refresh();

		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create([
			"inventory_id" => $inventory->id,
		]);
		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$receivingItem->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Receiving ID: {$receivingItem->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_invoice_item_bulk_add_unit_price_at_time(): void
	{
		/** @var Item $item */
		$item = Item::factory()->create();
		/** @var Location $location */
		$location = Location::factory()->create();
		/** @var Invoice $invoice */
		Invoice::factory()->create(["location_id" => $location->id]);
		/** @var Inventory $inventory */
		$inventory = Inventory::factory()->create([
			"status_id" => 3,
			"location_id" => $location->id,
			"item_id" => $item->id,
		]);

		$query = '
    		mutation {
    		  invoiceItemBulkAdd(input: {item: 1, provider: 1, invoiceIds: [1], quantity: 1}) {
    		    data {
    		      id
    		      name
    		      number
    		      price
    		    }
    		  }
    		}
		';
		$response = $this->postGraphqlJson($query);
		$firstNewInvoiceId = \Arr::get(
			json_decode($response->getContent(), true),
			"data.invoiceItemBulkAdd.data.0.id",
		);
		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create([
			"inventory_id" => $inventory->id,
			"invoice_item_id" => $firstNewInvoiceId,
		]); //, 'invoice_item_id' => $invoiceItem->id

		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$inventoryTransaction->invoiceItem->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Invoice ID: {$inventoryTransaction->invoiceItem->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_invoice_item_create_unit_price_at_time(): void
	{
		/** @var Location $location */
		$location = Location::factory()->create();
		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create();
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
				"invoice" => $inventoryTransaction->invoiceItem->invoice_id,
				"item" => $inventoryTransaction->invoiceItem->item_id,
				"provider" => 0,
				"quantity" => 1,
			],
			"location" => $location->id,
		];
		$this->postGraphqlJsonWithVariables($query, $variables);

		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$inventoryTransaction->invoiceItem->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Invoice ID: {$inventoryTransaction->invoiceItem->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_invoice_item_update_unit_price_at_time(): void
	{
		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create();
		$query = '
              mutation InvoiceItemUpdate($input: invoiceItemUpdateInput!) {
                invoiceItemUpdate(input: $input) {
                  data {
                    id
                  }
                }
              }
        ';
		//assume there is only one id and the id is 1
		$variables = [
			"input" => [
				"id" => $inventoryTransaction->invoiceItem->invoice_id,
				"chart" => 0,
				"chartType" => "",
				"description" => "",
				"quantity" => "55",
				"price" => 0,
				"administeredDate" => "2022-09-29",
				"discountPercent" => 0,
				"discountAmount" => 0,
				"unitPrice" => random_int(10, 1000),
				"dispensingFee" => 0,
				"hideFromRegister" => false,
				"serialNumber" => null,
				"allowExcessiveQuantity" => false,
				"provider" => 0,
			],
		];
		$this->postGraphqlJsonWithVariables($query, $variables);
		$inventoryTransaction->invoiceItem->refresh();
		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$inventoryTransaction->invoiceItem->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Invoice ID: {$inventoryTransaction->invoiceItem->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_item_create_unit_price_at_time(): void
	{
		$response = $this->create_item();
		$itemId = \Arr::get(
			json_decode($response->baseResponse->getContent(), true),
			"data.itemCreate.data.0.id",
		);

		/** @var Inventory $inventory */
		$inventory = Inventory::factory()->create(["item_id" => $itemId]);
		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create([
			"inventory_id" => $inventory->id,
		]);

		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$inventoryTransaction->inventory->item->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Item ID: {$inventoryTransaction->inventory->item->id}",
			$unitPriceData["additionalComment"],
		);
	}

	public function test_item_update_unit_price_at_time(): void
	{
		/** @var InventoryTransaction $inventoryTransaction */
		$inventoryTransaction = InventoryTransaction::factory()->create();
		/** @var Location $location */
		$location = Location::factory()->create();
		$query = '
			mutation itemUpdateMutation($id: ID, $unitPriceDisabled: Boolean!, $input: ItemUpdateInput!) {
                itemUpdate(id: $id, unitPriceDisabled: $unitPriceDisabled, input: $input) {
                    data {
                        id,
                        name,
                        unitPrice,
                        itemTypeId                      
                    }
                }
            }
		';
		$variables = [
			"id" => $inventoryTransaction->inventory->item->id,
			"input" => [
				"name" => "Test name changed",
				"number" => "123453",
				"itemTypeId" => $inventoryTransaction->inventory->item->type_id,
				"chartOfAccount" => "2",
				"categoryId" =>
					$inventoryTransaction->inventory->item->category_id,
				"description" => "",
				"allowAltDescription" => false,
				"isVaccine" => false,
				"isPrescription" => false,
				"isSerialized" => false,
				"isControlledSubstance" => false,
				"isInWellnessPlan" => false,
				"isEuthanasia" => false,
				"isReproductive" => false,
				"requiresProvider" => false,
				"hideFromRegister" => false,
				"costPrice" => 0,
				"minimumSaleAmount" => 0,
				"markupPercentage" => 0,
				"dispensingFee" => 0,
				"unitPrice" => random_int(10, 1000),
				"isNonTaxable" => false,
				"applyToRemainder" => false,
				"reminderIntervalId" =>
					$inventoryTransaction->inventory->item
						->reminder_interval_id,
				"reminderReplaces" => [],
				"minimumOnHand" => 0,
				"maximumOnHand" => 0,
				"nextTagNumber" => null,
				"drugIdentifier" => "",
				"isSingleLineKit" => false,
				"itemSpeciesRestrictions" => [],
				"itemLocations" => [
					[
						"id" => $location->id,
					],
				],
				"itemVolumePricing" => [
					[
						"id" => 0,
						"quantity" => 1,
						"unitPrice" => 23,
					],
					[
						"id" => 0,
						"quantity" => 3,
						"unitPrice" => 44,
					],
				],
				"itemKitItems" => [],
			],
			"unitPriceDisabled" => false,
		];
		$this->postGraphqlJsonWithVariables($query, $variables);

		$unitPriceData = $inventoryTransaction->getUnitPriceData();
		$inventoryTransaction->inventory->item->refresh();
		self::assertNotSame("", $unitPriceData["changedBy"]);
		self::assertSame(
			$inventoryTransaction->inventory->item->unit_price,
			$unitPriceData["unitPrice"],
		);
		self::assertSame(
			"Item ID: {$inventoryTransaction->inventory->item->id}",
			$unitPriceData["additionalComment"],
		);
	}
}
