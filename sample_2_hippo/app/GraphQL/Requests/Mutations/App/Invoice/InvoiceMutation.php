<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\InventoryTransactionStatus;
use App\Models\Invoice;
use App\Models\InvoiceAppliedDiscount;
use App\Models\ItemType;
use App\Models\Credit;

abstract class InvoiceMutation extends AppHippoMutation
{
	protected $model = Invoice::class;
	protected $nonStockingItemType;

	protected function reprocessDiscountsTaxesAndTotals($invoice)
	{
		if (!$invoice) {
			return;
		}

		$invoice->refresh();
		$invoice->load(
			"appliedDiscounts",
			"invoiceItems.itemType",
			"invoiceItems.invoiceItemTaxes",
			"invoiceItems.inventoryTransactions.inventory",
		);
		$this->removeExistingAppliedInvoiceDiscounts($invoice);
		$this->applyDiscountsAndCalculatePreTaxLineItemTotals($invoice);
		$this->reapplyTaxes($invoice);
		$this->recalculateInvoiceTotal($invoice);
		$invoice->push();
	}

	protected function removeExistingAppliedInvoiceDiscounts($invoice)
	{
		foreach ($invoice->appliedDiscounts as $invoiceAppliedDiscount) {
			$invoiceAppliedDiscount->delete();
		}

		foreach ($invoice->invoiceItems as $invoiceItem) {
			$invoiceItem->discount_amount = 0;
		}

		$invoice->push();
	}

	protected function setBaseInvoiceItemTotal($invoiceItem)
	{
		$quantity = abs($invoiceItem->quantity);

		if ($invoiceItem->volume_price && $invoiceItem->volume_quantity) {
			if ($invoiceItem->apply_discount_to_remainder) {
				$basePrice = $invoiceItem->volume_price * $quantity;
			} else {
				$discountedPrice =
					$invoiceItem->volume_price * $invoiceItem->volume_quantity;
				$remainderPrice =
					$invoiceItem->price *
					($quantity - $invoiceItem->volume_quantity);

				$basePrice = $discountedPrice + $remainderPrice;
			}
		} else {
			$basePrice = $invoiceItem->price * $quantity;
		}

		if ($basePrice < $invoiceItem->minimum_sale_amount) {
			$basePrice = $invoiceItem->minimum_sale_amount;
		}

		$postPercentDiscountPrice =
			$basePrice * (1 - $invoiceItem->discount_percent / 100);

		$percentDiscountAmount = $basePrice - $postPercentDiscountPrice;

		$priceWithDispensingFee =
			$postPercentDiscountPrice + $invoiceItem->dispensing_fee;
		$invoiceItem->discount_amount = $percentDiscountAmount;
		$invoiceItem->total =
			$invoiceItem->quantity < 0
				? $priceWithDispensingFee * -1
				: $priceWithDispensingFee;

		$invoiceItem->save();
	}

	/**
	 * Big O(n^2)
	 *
	 * @param \App\Models\Invoice $invoice
	 */
	protected function applyDiscountsAndCalculatePreTaxLineItemTotals($invoice)
	{
		foreach ($invoice->invoiceItems as $invoiceItem) {
			if ($invoiceItem->itemType->name !== "Discount Code") {
				$this->setBaseInvoiceItemTotal($invoiceItem);
			}
		}

		$invoice->push();

		foreach ($invoice->invoiceItems as $discountCode) {
			if ($discountCode->itemType->id !== 8) {
				continue;
			}

			$baseDiscountAmount =
				$discountCode->price * $discountCode->quantity;
			$currentlyApplied = 0;

			foreach ($invoice->invoiceItems as $regularItem) {
				if ($regularItem->itemType->name === "Discount Code") {
					continue;
				}

				$remainingDiscountAmount =
					$baseDiscountAmount - $currentlyApplied;

				// is the discount code broke NOW?
				if ($remainingDiscountAmount <= 0) {
					continue;
				}

				if ($regularItem->total > $remainingDiscountAmount) {
					$amountToApply = $remainingDiscountAmount;
				} else {
					$amountToApply = $regularItem->total;
				}

				InvoiceAppliedDiscount::on($this->subdomainName)->create([
					"invoice_id" => $invoice->id,
					"discount_invoice_item_id" => $discountCode->id,
					"adjusted_invoice_item_id" => $regularItem->id,
					"amount_applied" => $amountToApply,
				]);

				$currentlyApplied = $currentlyApplied + $amountToApply;

				$priceAfterDiscountCode = $regularItem->total - $amountToApply;
				$totalDiscount = $regularItem->discount_amount + $amountToApply;
				$regularItem->discount_amount = $totalDiscount;
				$regularItem->total = $priceAfterDiscountCode;
				$regularItem->save();
			}
		}

		$invoice->push();
	}

	protected function reapplyTaxes($invoice)
	{
		foreach ($invoice->invoiceItems as $invoiceItem) {
			// Won't add taxes if there are no associated taxes
			foreach ($invoiceItem->invoiceItemTaxes as $invoiceItemTax) {
				$invoiceItemTax->amount =
					$invoiceItem->total * ($invoiceItemTax["percent"] / 100);
				$invoiceItemTax->save();
			}
		}

		$invoice->push();
	}

	protected function recalculateInvoiceTotal($invoice)
	{
		$total = 0;

		foreach ($invoice->invoiceItems as $index => $invoiceItem) {
			if ($invoiceItem->itemType->name === "Discount Code") {
				continue;
			}

			$total += $invoiceItem->total;

			foreach ($invoiceItem->invoiceItemTaxes as $invoiceItemTax) {
				$total += $invoiceItemTax->amount;
			}
		}

		$invoice->total = $total;

		$invoice->push();
	}

	protected function completeInventoryTransactions($invoiceItem)
	{
		$completeInventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Complete")
			->firstOrFail();

		foreach ($invoiceItem->inventoryTransactions as $inventoryTransaction) {
			$inventoryTransaction->status_id =
				$completeInventoryTransactionStatus->id;

			if ($inventoryTransaction->inventory->remaining_quantity <= 0) {
				$inventoryTransaction->inventory->is_open = false;
				$inventoryTransaction->inventory->save();
			}

			$inventoryTransaction->save();
		}
	}

	protected function removeInventoryTransactions($invoiceItem)
	{
		$voidedInventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Voided")
			->firstOrFail();

		foreach ($invoiceItem->inventoryTransactions as $inventoryTransaction) {
			if ($inventoryTransaction->inventory) {
				$inventoryTransaction->inventory->remaining_quantity -=
					$inventoryTransaction->quantity;
				$inventoryTransaction->inventory->save();
			}

			$inventoryTransaction->status_id =
				$voidedInventoryTransactionStatus->id;
			$inventoryTransaction->save();
			$inventoryTransaction->delete();
		}
	}

	protected function calculateInvoiceItemTotal($invoiceItem)
	{
		$calculatedUnitPrice =
			$invoiceItem->price - $invoiceItem->discount_amount;

		$total = $invoiceItem->quantity * $calculatedUnitPrice;

		$total = $total + $invoiceItem->dispensing_fee;

		if ($total < 0) {
			$total = 0;
		}

		if ($total < $invoiceItem->minimum_sale_amount) {
			$total = $invoiceItem->minimum_sale_amount;
		}

		return $total;
	}

	protected function createCredit($invoiceItem, $ownerId)
	{
		$creditDetails = [
			"number" => Credit::generate_id(),
			"value" => $invoiceItem->price,
			"original_value" => $invoiceItem->price,
		];

		switch ($invoiceItem->type_id) {
			case ItemType::GIFT_CARD:
				$creditDetails["type"] = "Gift Card";
				break;
			case ItemType::ACCOUNT_CREDIT:
				$creditDetails["type"] = "Account Credit";
				$creditDetails["owner_id"] = $ownerId;
				break;
		}

		$credit = new Credit();
		$credit->setConnection($this->subdomainName);

		return $credit->create($creditDetails)->id;
	}

	protected function getInvoices($invoiceIds, $sortDirection = 0)
	{
		$invoiceQuery = Invoice::on($this->subdomainName)->whereIn(
			"id",
			$invoiceIds,
		);

		// 0 === desc
		if ($sortDirection === 0) {
			$invoiceQuery->orderBy("created_at", "desc");
		} else {
			$invoiceQuery->orderBy("created_at", "asc");
		}

		return $invoiceQuery->get();
	}
}
