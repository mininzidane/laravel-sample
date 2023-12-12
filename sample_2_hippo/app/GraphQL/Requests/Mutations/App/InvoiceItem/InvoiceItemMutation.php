<?php

namespace App\GraphQL\Requests\Mutations\App\InvoiceItem;

use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\Invoice\InvoiceMutation;
use App\Models\EmailChart;
use App\Models\Gender;
use App\Models\HistoryChart;
use App\Models\Inventory;
use App\Models\InventoryStatus;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransactionStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\PhoneChart;
use App\Models\ProgressChart;
use App\Models\Reminder;
use App\Models\SoapChart;
use App\Models\TreatmentChart;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class InvoiceItemMutation extends InvoiceMutation
{
	protected $model = InvoiceItem::class;

	protected function fetchChart($subdomainName, $chartType, $chartId)
	{
		$chartClass = null;

		switch ($chartType) {
			case "phone":
				$chartClass = PhoneChart::class;
				break;
			case "progress":
				$chartClass = ProgressChart::class;
				break;
			case "treatment":
				$chartClass = TreatmentChart::class;
				break;
			case "history":
				$chartClass = HistoryChart::class;
				break;
			case "email":
				$chartClass = EmailChart::class;
				break;
			case "soap":
			default:
				$chartClass = SoapChart::class;
				break;
		}

		$chart = $chartClass::on($subdomainName)->find($chartId);

		if (!$chart) {
			throw new Exception("The requested chart does not exist");
		}

		return $chart;
	}

	public function createTransactionsForDispensedQuantity(
		$invoice,
		$quantityToCreateTransactionsFor,
		$invoiceItem
	) {
		if ($invoice->status_id === Invoice::ESTIMATE_STATUS) {
			$inventoryTransactionStatus = InventoryTransactionStatus::on(
				$this->subdomainName,
			)
				->where("name", "Estimate")
				->firstOrFail();
		} else {
			$inventoryTransactionStatus = InventoryTransactionStatus::on(
				$this->subdomainName,
			)
				->where("name", "Pending")
				->firstOrFail();
		}

		$userId = Auth::guard("api-subdomain-passport")->user()->id;

		if ($quantityToCreateTransactionsFor < 0) {
			$completeStatus = InventoryStatus::on($this->subdomainName)
				->where("name", "Complete")
				->firstOrFail();

			$returnInventory = Inventory::on($this->subdomainName)->create([
				"item_id" => $invoiceItem->item->id,
				"receiving_item_id" => null,
				"location_id" => $invoice->location->id,
				"status_id" => $completeStatus->id,
				"lot_number" => null,
				"serial_number" => null,
				"expiration_date" => null,
				"starting_quantity" => 0,
				"remaining_quantity" => -1 * $quantityToCreateTransactionsFor,
				"is_open" => false,
				"opened_at" => null,
			]);

			$returnInventoryTransaction = InventoryTransaction::on(
				$this->subdomainName,
			)->create([
				"inventory_id" => $returnInventory->id,
				"user_id" => $userId,
				"invoice_item_id" => $invoiceItem->id,
				"status_id" => $inventoryTransactionStatus->id,
				"quantity" => -1 * $quantityToCreateTransactionsFor,
				"comment" => "Returned Items",
			]);

			$invoiceItem
				->inventoryTransactions()
				->save($returnInventoryTransaction);

			return;
		}

		while ($quantityToCreateTransactionsFor > 0) {
			$selectedInventory = $this->selectInventory($invoice, $invoiceItem);

			if ($selectedInventory) {
				$quantityUsed = min(
					$quantityToCreateTransactionsFor,
					$selectedInventory->remaining_quantity,
				);

				$selectedInventory->remaining_quantity -= $quantityUsed;
				$selectedInventory->save();

				$inventoryId = $selectedInventory->id;
			} else {
				$completeStatus = InventoryStatus::on($this->subdomainName)
					->where("name", "Complete")
					->firstOrFail();

				$excessiveInventory = Inventory::on(
					$this->subdomainName,
				)->create([
					"item_id" => $invoiceItem->item->id,
					"receiving_item_id" => null,
					"location_id" => $invoice->location->id,
					"status_id" => $completeStatus->id,
					"lot_number" => null,
					"serial_number" => null,
					"expiration_date" => null,
					"starting_quantity" => 0,
					"remaining_quantity" =>
						-1 * $quantityToCreateTransactionsFor,
					"is_open" => true,
					"opened_at" => Carbon::now(),
				]);

				$quantityUsed = $quantityToCreateTransactionsFor;
				$inventoryId = $excessiveInventory->id;
			}

			$inventoryTransactionDetails = [
				"inventory_id" => $inventoryId,
				"user_id" => $userId,
				"invoice_item_id" => $invoiceItem->id,
				"status_id" => $inventoryTransactionStatus->id,
				"quantity" => -1 * $quantityUsed,
				"comment" => null,
			];

			$newInventoryTransaction = InventoryTransaction::on(
				$this->subdomainName,
			)->create($inventoryTransactionDetails);

			$invoiceItem
				->inventoryTransactions()
				->save($newInventoryTransaction);

			$quantityToCreateTransactionsFor -= $quantityUsed;
		}
	}

	protected function selectInventory($invoice, $invoiceItem)
	{
		$item = $invoiceItem->item;

		// Default to first open
		$nonZeroQuantityInventories = $item->inventory
			->where("status_id", "=", 3)
			->where("remaining_quantity", ">", 0)
			->where("location_id", "=", $invoice->location->id);

		$selectedInventory = $nonZeroQuantityInventories->firstWhere(
			"is_open",
			true,
		);

		// If none are open, default to soonest to expire
		if (!$selectedInventory) {
			$selectedInventory = $nonZeroQuantityInventories
				->sortBy("expiration_date")
				->firstWhere("expiration_date", "!=", null);
		}

		// If none have expiration dates, default to the first created in the system
		if (!$selectedInventory) {
			$selectedInventory = $nonZeroQuantityInventories
				->sortBy("created_at")
				->first();
		}

		if ($selectedInventory && !$selectedInventory->is_open) {
			$selectedInventory->update([
				"is_open" => true,
				"opened_at" => Carbon::now(),
			]);
		}

		return $selectedInventory;
	}

	public function deleteInvoiceItem($invoiceItemId)
	{
		$invoiceItemToDelete = InvoiceItem::on($this->subdomainName)
			->with("invoiceItemTaxes")
			->findOrFail($invoiceItemId);

		$invoice = $invoiceItemToDelete->invoice;

		foreach ($invoiceItemToDelete->invoiceItemTaxes as $invoiceItemTax) {
			$invoiceItemTax->delete();
		}

		if ($invoiceItemToDelete->hasInventory) {
			$this->removeInventoryTransactions($invoiceItemToDelete);
		}

		$this->finalizeInvoiceItemDelete(
			$invoiceItemToDelete,
			$invoiceItemToDelete->invoice->patient,
		);

		$invoiceItemToDelete->delete();

		$this->reprocessDiscountsTaxesAndTotals($invoice);
	}

	public function finalizeInvoiceItemDelete($invoiceItem, $patient)
	{
		$this->finalizeInvoiceItemDeleteReproductive($invoiceItem, $patient);
		$this->finalizeInvoiceItemDeleteEuthanasia($invoiceItem, $patient);
		$this->finalizeInvoiceItemDeleteReminders($invoiceItem, $patient);
	}

	protected function finalizeInvoiceItemDeleteReproductive(
		$invoiceItem,
		$patient
	) {
		if (
			!$invoiceItem->is_reproductive ||
			!$patient->gender_relation ||
			!$patient->gender_relation->neutered
		) {
			return;
		}

		$newGender = Gender::on($this->subdomainName)
			->where("species", $patient->gender_relation->species)
			->where("sex", $patient->gender_relation->sex)
			->where("neutered", 0)
			->firstOrFail();

		$patient->gender_relation()->associate($newGender);
		$patient->save();
	}

	protected function finalizeInvoiceItemDeleteEuthanasia(
		$invoiceItem,
		$patient
	) {
		if (!$invoiceItem->is_euthanasia || !$patient->deceased) {
			return;
		}

		$patient->date_of_death = null;
		$patient->save();
	}

	protected function finalizeInvoiceItemDeleteReminders(
		$invoiceItem,
		$patient
	) {
		$toBeRemovedReminders = Reminder::on($this->subdomainName)
			->where("item_id", $invoiceItem->item->id)
			->where("invoice_id", $invoiceItem->invoice->id)
			->where("client_id", $patient->id)
			->get();

		foreach ($toBeRemovedReminders as $toBeRemovedReminder) {
			$toBeRemovedReminder->removed = 1;
			$toBeRemovedReminder->save();
			$toBeRemovedReminder->delete();
		}
	}

	protected function handleInvoiceItemQuantityModifications(
		$invoiceItemToUpdate,
		$requestedQuantity,
		$allowExcessiveQuantity = false
	) {
		if ($invoiceItemToUpdate->quantity === $requestedQuantity) {
			return $invoiceItemToUpdate;
		}

		if ($invoiceItemToUpdate->hasInventory) {
			$item = Item::on($this->subdomainName)->findOrFail(
				$invoiceItemToUpdate->item->id,
			);
			$remainingQuantity = $item->inventory->sum("remaining_quantity");

			if (
				!$allowExcessiveQuantity &&
				$requestedQuantity >
					$remainingQuantity + $invoiceItemToUpdate->quantity
			) {
				throw new \Exception(
					"The requested quantity of " .
						$requestedQuantity .
						" exceeds the remaining " .
						$remainingQuantity .
						" in inventory.",
					HippoGraphQLErrorCodes::INVOICE_ITEM_INVENTORY_NOT_FOUND,
				);
			}
		}

		$invoiceItemToUpdate->item->load("itemVolumePricing");
		$volumePrice = $this->getVolumePrice(
			$invoiceItemToUpdate->item,
			$requestedQuantity,
		);

		$invoiceItemToUpdate->quantity = $requestedQuantity;
		$invoiceItemToUpdate->volume_price = $volumePrice->unit_price ?? null;
		$invoiceItemToUpdate->volume_quantity = $volumePrice->quantity ?? null;
		$invoiceItemToUpdate->save();

		if ($invoiceItemToUpdate->hasInventory) {
			$this->removeInventoryTransactions($invoiceItemToUpdate);
			$this->createTransactionsForDispensedQuantity(
				$invoiceItemToUpdate->invoice,
				$requestedQuantity,
				$invoiceItemToUpdate,
			);
		}

		return $invoiceItemToUpdate;
	}

	protected function handleInvoiceItemChartModifications(
		$invoiceItemToUpdate,
		$chartType,
		$chartId
	) {
		// If a chart has changed
		if (
			$this->chartHasChanged($invoiceItemToUpdate, $chartType, $chartId)
		) {
			// and it is no longer set, dissociate the existing chart
			if ($chartId === 0 || $chartType === "") {
				$invoiceItemToUpdate->chart()->dissociate();
			} else {
				// if a new chart is provided, replace the old one
				$chartToAssociate = $this->fetchChart(
					$this->subdomainName,
					$chartType,
					$chartId,
				);

				$invoiceItemToUpdate->chart()->dissociate();
				$invoiceItemToUpdate->chart()->associate($chartToAssociate);
			}
		}

		return $invoiceItemToUpdate;
	}

	protected function chartHasChanged(
		$invoiceItemToUpdate,
		$chartType,
		$chartId
	) {
		// no initial chart
		if (!$invoiceItemToUpdate->chart) {
			// and there still isn't one
			if ($chartId === 0 || $chartType === "") {
				return false;
			}

			// but there is one now
			return true;
		}

		// had initial chart, but now doesn't
		if ($chartId === 0 && $chartType === "") {
			return true;
		}

		// had initial chart and it changed
		return $invoiceItemToUpdate->chart->id !== $chartId ||
			$invoiceItemToUpdate->chart->chartType !== $chartType;
	}

	protected function handleInvoiceItemProviderModifications(
		$invoiceItemToUpdate,
		$providerId = null
	) {
		if (!$providerId) {
			$invoiceItemToUpdate->provider()->dissociate();
			return $invoiceItemToUpdate;
		}

		if (!$invoiceItemToUpdate->provider) {
			$providerToAssociate = User::on($this->subdomainName)->findOrFail(
				$providerId,
			);
			$invoiceItemToUpdate->provider()->associate($providerToAssociate);
		} elseif ($invoiceItemToUpdate->provider->id !== $providerId) {
			$providerToAssociate = User::on($this->subdomainName)->findOrFail(
				$providerId,
			);
			$invoiceItemToUpdate->provider()->dissociate();
			$invoiceItemToUpdate->provider()->associate($providerToAssociate);
		}

		return $invoiceItemToUpdate;
	}

	protected function processSpeciesRestrictions($patient, $item)
	{
		if ($item->itemSpeciesRestrictions->isEmpty()) {
			return true;
		}

		$speciesAllowed = $item->itemSpeciesRestrictions->contains(function (
			$value,
			$index
		) use ($patient) {
			return $value->species->name === $patient->species;
		});

		if (!$speciesAllowed) {
			throw new \Exception(
				"Item is restricted for this patient's species.",
				HippoGraphQLErrorCodes::INVOICE_ADD_ITEM_SPECIES_RESTRICTED,
			);
		}

		return true;
	}

	protected function prepareProvider($providerId, $item)
	{
		if ($item->requires_provider === 1 && !$providerId) {
			throw new \Exception(
				"Please select a provider for this item",
				HippoGraphQLErrorCodes::INVOICE_ADD_ITEM_MISSING_PROVIDER,
			);
		}

		$provider = User::on($this->subdomainName)->find($providerId);

		if ($provider && !$provider->isProvider) {
			throw new \Exception(
				"The prescribing user must be a provider",
				HippoGraphQLErrorCodes::USER_NOT_PROVIDER,
			);
		}

		return $provider;
	}

	protected function prepareChart($chartType, $chartId)
	{
		if ($chartId <= 0 || $chartType === "") {
			return null;
		}

		return $this->fetchChart($this->subdomainName, $chartType, $chartId);
	}

	protected function getVolumePrice($item, $quantity)
	{
		if (!$item->itemVolumePricing) {
			return null;
		}

		return $item->itemVolumePricing
			->sortBy("quantity")
			->last(function ($value, $key) use ($quantity) {
				return $quantity >= $value->quantity;
			});
	}
}
