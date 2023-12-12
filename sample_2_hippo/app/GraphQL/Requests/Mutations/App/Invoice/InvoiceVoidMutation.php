<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceVoidMutation extends InvoiceMutation
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoices: Delete";

	protected $attributes = [
		"name" => "InvoiceVoid",
		"model" => Invoice::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_VOID;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" => "The specified invoice does not exist",
			"input.id.required" => "An invoice must be provided",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceVoidInput"),
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

		if (sizeof($invoice->invoicePayments) > 0) {
			throw new Exception(
				"Invoices with payments cannot be removed",
				HippoGraphQLErrorCodes::INVOICE_VOID_PAYMENTS_EXIST,
			);
		}

		$this->removeExistingAppliedInvoiceDiscounts($invoice);

		foreach ($invoice->invoiceItems as $invoiceItem) {
			if ($invoiceItem->hasInventory) {
				$this->removeInventoryTransactions($invoiceItem);
			}

			foreach ($invoiceItem->invoiceItemTaxes as $invoiceItemTax) {
				$invoiceItemTax->delete();
			}

			$invoiceItem->delete();
		}

		$voidedInvoiceStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Voided")
			->firstOrFail();

		$invoice->status_id = $voidedInvoiceStatus->id;
		$invoice->active = 0;
		$invoice->save();
		$invoice->delete();

		$this->affectedId = $invoice->id;

		return $this->model
			::on($this->subdomainName)
			->where($invoice->getPrimaryKey(), $invoice->id)
			->paginate(1);
	}
}
