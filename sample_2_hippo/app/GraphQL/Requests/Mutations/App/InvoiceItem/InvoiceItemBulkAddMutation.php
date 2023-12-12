<?php

namespace App\GraphQL\Requests\Mutations\App\InvoiceItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceItemBulkAddMutation extends InvoiceItemAddMutation
{
	protected $model = InvoiceItem::class;

	protected $permissionName = "Invoice Items: Create";

	protected $attributes = [
		"name" => "InvoiceItemBulkAdd",
		"model" => InvoiceItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_ITEM_BULK_ADD;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.item.exists" => "Please select a valid item to add",
			"input.invoiceIds.required" => "Please select at least one invoice",
			"input.quantity.min" => "Please specify a positive quantity",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceItemBulkAddInput"),
			],
		];
	}

	/**
	 * @param string $root
	 * @param array $args
	 * @param array $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$item = Item::on($this->subdomainName)->findOrFail(
			$this->args["input"]["item"],
		);

		$invoiceItemIds = [];

		$provider = $this->prepareProvider(
			$this->fetchInput("provider", null),
			$item,
		);

		$totalQuantity =
			sizeof($this->args["input"]["invoiceIds"]) *
			$this->args["input"]["quantity"];
		$firstInvoice = Invoice::on($this->subdomainName)->findOrFail(
			$this->args["input"]["invoiceIds"][0],
		);

		$administeredDate = Carbon::createFromDate(
			$this->fetchInput(
				"administeredDate",
				Carbon::now($firstInvoice->location->tz->php_supported),
			),
		)->toDateString();

		$this->checkTotalBulkQuantity(
			$totalQuantity,
			$item,
			$firstInvoice->location,
			$this->args["input"]["allowExcessiveQuantity"] ?? false,
		);

		foreach ($this->args["input"]["invoiceIds"] as $invoiceId) {
			$invoice = Invoice::on($this->subdomainName)->findOrFail(
				$invoiceId,
			);

			//add all parameters
			if ($item->itemType->name === "Item Kit") {
				$invoiceItemIds = $this->createInvoiceItemsForItemKit(
					$invoice,
					$item,
					null,
					$provider,
					$administeredDate,
				);
			} else {
				$invoiceItemIds = $this->createInvoiceItemForSingleItem(
					$invoice,
					$item,
					null,
					$provider,
					$administeredDate,
				);
			}

			$this->reprocessDiscountsTaxesAndTotals($invoice);
		}

		$this->affectedId = $invoiceItemIds;

		return InvoiceItem::on($this->subdomainName)
			->whereIn("id", $invoiceItemIds)
			->paginate(1);
	}

	protected function checkTotalBulkQuantity(
		$quantity,
		$item,
		$location,
		$allowExcessiveQuantity = false
	) {
		if ($item->hasInventory && !$allowExcessiveQuantity) {
			$this->checkRemainingQuantity($quantity, $item, $location);
		}
	}
}
