<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\InventoryTransactionStatus;
use App\Models\Invoice;
use App\Models\InvoiceItemTax;
use App\Models\InvoiceStatus;
use App\Models\Patient;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\Requests\Mutations\App\InvoiceItem\InvoiceItemAddMutation;
use App\GraphQL\Requests\Mutations\App\InvoiceItem\InvoiceItemDeleteMutation;
use Illuminate\Support\Carbon;

class InvoiceSaveDetailsMutation extends InvoiceMutation
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoices: Update";

	protected $attributes = [
		"name" => "InvoiceSaveDetails",
		"model" => Invoice::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_SAVE_DETAILS;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" => "The specified invoice does not exist",
			"input.id.required" => "An invoice must be provided",
			"input.isEstimate.required" => "An estimate value must be provided",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceSaveDetailsInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$invoice = Invoice::on($this->subdomainName)->findOrFail(
			$this->args["input"]["id"],
		);

		$initialTaxableStatus = $invoice->is_taxable;
		$newTaxableStatus = $this->args["input"]["is_taxable"];
		$newEstimateStatus = $this->args["input"]["is_estimate"];
		unset($this->args["input"]["is_estimate"]);

		$this->verifyInvoiceCanBeMadeEstimate($invoice, $newEstimateStatus);

		$currentEstimateStatus = $invoice->invoiceStatus->name === "Estimate";

		$invoiceUpdateDetails = [
			"comment" => $this->args["input"]["comment"],
			"print_comment" => $this->args["input"]["print_comment"],
			"is_taxable" => $newTaxableStatus,
		];

		if ($newEstimateStatus && !$currentEstimateStatus) {
			// setting invoice to estimate
			$this->switchInvoiceToEstimate($invoice);
		}

		if (!$newEstimateStatus && $currentEstimateStatus) {
			// removing estimate status
			$this->switchInvoiceToOpen($invoice);
		}

		if ($initialTaxableStatus != $newTaxableStatus) {
			if ($newTaxableStatus) {
				$this->switchInvoiceToTaxable($invoice);
			} else {
				$this->switchInvoiceToNonTaxable($invoice);
			}

			$this->reprocessDiscountsTaxesAndTotals($invoice);
		}

		$invoice->update($invoiceUpdateDetails);

		$this->affectedId = $this->args["input"]["id"];

		return Invoice::on($this->subdomainName)
			->where("id", $this->args["input"]["id"])
			->paginate(1);
	}

	protected function verifyInvoiceCanBeMadeEstimate(
		$invoice,
		$newEstimateStatus
	) {
		if (sizeof($invoice->invoicePayments) > 0 && $newEstimateStatus) {
			throw new Exception(
				"This invoice already has applied payments and cannot be made an estimate",
				HippoGraphQLErrorCodes::INVOICE_UPDATE_CANNOT_BE_ESTIMATE,
			);
		}
	}

	protected function switchInvoiceToEstimate($invoice)
	{
		$estimateInvoiceStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Estimate")
			->firstOrFail();

		$invoice->status_id = $estimateInvoiceStatus->id;

		$estimateInventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Estimate")
			->firstOrFail();

		foreach ($invoice->invoiceItems as $invoiceItem) {
			$deleteMutation = new InvoiceItemDeleteMutation();
			$deleteMutation->subdomainName = $this->subdomainName;

			if ($invoiceItem->inventoryTransactions) {
				foreach (
					$invoiceItem->inventoryTransactions
					as $inventoryTransaction
				) {
					$inventoryTransaction->status_id =
						$estimateInventoryTransactionStatus->id;
					$inventoryTransaction->save();

					// if item is stocking type
					if ($invoiceItem->hasInventory) {
						$deleteMutation->createTransactionsForDispensedQuantity(
							$invoice,
							$invoiceItem->quantity * -1,
							$invoiceItem,
						);
					}
				}
			}

			$deleteMutation->finalizeInvoiceItemDelete(
				$invoiceItem,
				$invoice->patient,
			);
		}

		$invoice->push();
	}

	protected function switchInvoiceToOpen($invoice)
	{
		$openInvoiceStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Open")
			->firstOrFail();

		$invoice->status_id = $openInvoiceStatus->id;
		$invoice->created_at = Carbon::now();

		$invoice->invoiceItems()->update([
			"administered_date" => Carbon::now(
				$invoice->location->tz->php_supported,
			)->toDateString(),
		]);

		$openInventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Pending")
			->firstOrFail();

		foreach ($invoice->invoiceItems as $invoiceItem) {
			$addMutation = new InvoiceItemAddMutation();
			$addMutation->subdomainName = $this->subdomainName;

			if ($invoiceItem->inventoryTransactions->count()) {
				foreach (
					$invoiceItem->inventoryTransactions
					as $inventoryTransaction
				) {
					$inventoryTransaction->status_id =
						$openInventoryTransactionStatus->id;
					$inventoryTransaction->save();
				}
			} else {
				// if item is stocking type
				if ($invoiceItem->hasInventory) {
					$addMutation->createTransactionsForDispensedQuantity(
						$invoice,
						$invoiceItem->quantity,
						$invoiceItem,
					);
				}
			}

			$addMutation->finalizeInvoiceItemAdd(
				$invoiceItem,
				$invoice->patient,
				true,
				true,
				true,
			);
		}

		$invoice->push();
	}

	protected function switchInvoiceToTaxable($invoice)
	{
		foreach ($invoice->invoiceItems as $invoiceItem) {
			foreach ($invoiceItem->item->itemTaxes as $itemTax) {
				InvoiceItemTax::on($this->subdomainName)->create([
					"invoice_item_id" => $invoiceItem->id,
					"tax_id" => $itemTax->tax_id,
					"name" => $itemTax->tax->name,
					"percent" => $itemTax->tax->percent,
					"amount" =>
						$invoiceItem->total * ($itemTax->tax->percent / 100),
				]);
			}
		}
	}

	protected function switchInvoiceToNonTaxable($invoice)
	{
		foreach ($invoice->invoiceItems as $invoiceItem) {
			foreach ($invoiceItem->invoiceItemTaxes as $invoiceItemTax) {
				$invoiceItemTax->delete();
			}
		}
	}
}
