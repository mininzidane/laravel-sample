<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\InventoryTransactionStatus;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceReopenMutation extends InvoiceMutation
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoices: Update";

	protected $attributes = [
		"name" => "InvoiceReopen",
		"model" => Invoice::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_REOPEN;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoiceId.exists" => "The specified invoice does not exist",
			"input.invoiceId.required" => "An invoice must be provided",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceReopenInput"),
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
		$openStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Open")
			->firstOrFail();

		$invoiceToReopen = Invoice::on($this->subdomainName)->findOrFail(
			$this->args["input"]["invoiceId"],
		);

		if ($invoiceToReopen->invoiceStatus->name === "Open") {
			throw new \Exception(
				"The Invoice is Already Open",
				HippoGraphQLErrorCodes::INVOICE_REOPEN_ON_OPEN,
			);
		}

		$patientId = $invoiceToReopen->patient->id;

		Invoice::on($this->subdomainName)
			->where("patient_id", $patientId)
			->update(["active" => 0]);

		$invoiceToReopen->update([
			"status_id" => $openStatus->id,
			"completed_at" => null,
			"active" => 1,
		]);

		foreach ($invoiceToReopen->invoiceItems as $invoiceItem) {
			if ($invoiceItem->hasInventory) {
				$this->reopenInventoryTransactions($invoiceItem);
			}
		}

		$this->affectedId = $this->args["input"]["invoiceId"];

		return Invoice::on($this->subdomainName)
			->where("id", $this->args["input"]["invoiceId"])
			->paginate(1);
	}

	protected function reopenInventoryTransactions($invoiceItem)
	{
		$completeInventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Pending")
			->firstOrFail();

		foreach ($invoiceItem->inventoryTransactions as $inventoryTransaction) {
			$inventoryTransaction->status_id =
				$completeInventoryTransactionStatus->id;
			$inventoryTransaction->save();
		}
	}
}
