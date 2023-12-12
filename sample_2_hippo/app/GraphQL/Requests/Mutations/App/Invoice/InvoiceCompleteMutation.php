<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\ItemType;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceCompleteMutation extends InvoiceMutation
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoices: Update";

	protected $attributes = [
		"name" => "InvoiceComplete",
		"model" => Invoice::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_COMPLETE;

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
				"type" => GraphQL::type("InvoiceCompleteInput"),
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
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$completeStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Complete")
			->firstOrFail();

		$invoiceToUpdate = Invoice::on($this->subdomainName)
			->where("id", $this->args["input"]["id"])
			->firstOrFail();
		$completionTimestamp = Carbon::now();
		$update = [
			"status_id" => $completeStatus->id,
			"completed_at" => $completionTimestamp,
			"active" => 0,
		];
		if ($invoiceToUpdate->original_completed_at === null) {
			$update["original_completed_at"] = $completionTimestamp;
		}
		$invoiceToUpdate->update($update);

		foreach ($invoiceToUpdate->invoiceItems as $invoiceItem) {
			// check length of serial number, 0 was being treated as falsy
			if (
				$invoiceItem->is_serialized &&
				strlen($invoiceItem->serial_number) === 0
			) {
				throw new Exception(
					"Please provide serial numbers for all serialized items before completing this invoice",
					HippoGraphQLErrorCodes::INVOICE_COMPLETE_MISSING_SERIAL,
				);
			}

			if (
				in_array($invoiceItem->type_id, [
					ItemType::GIFT_CARD,
					ItemType::ACCOUNT_CREDIT,
				])
			) {
				$creditId = $this->createCredit(
					$invoiceItem,
					$invoiceToUpdate->owner_id,
				);

				$invoiceItem->update([
					"credit_id" => $creditId,
				]);
			}

			if ($invoiceItem->hasInventory) {
				$this->completeInventoryTransactions($invoiceItem);
			}
		}

		$this->affectedId = $this->args["input"]["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->args["input"]["id"])
			->paginate(1);
	}
}
