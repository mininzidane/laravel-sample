<?php

namespace App\GraphQL\Requests\Mutations\App\InvoiceItem;

use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceItemCreateMutation extends InvoiceItemAddMutation
{
	protected $model = InvoiceItem::class;

	protected $permissionName = "Invoice Items: Create";

	protected $attributes = [
		"name" => "InvoiceItemCreate",
		"model" => InvoiceItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_ITEM_CREATE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoice.exists" => "Please select a valid invoice",
			"input.item.exists" => "Please select a valid item to add",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceItemCreateInput"),
			],
		];
	}

	/**
	 * @param string $root
	 * @param array $args
	 * @param array $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return mixed|null
	 * @throws \Throwable
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$invoice = Invoice::on($this->subdomainName)->findOrFail(
			$this->args["input"]["invoice"],
		);
		$item = Item::on($this->subdomainName)->findOrFail(
			$this->args["input"]["item"],
		);

		$this->processSpeciesRestrictions($invoice->patient, $item);

		$provider = $this->prepareProvider(
			$this->fetchInput("provider", null),
			$item,
		);
		$chart = $this->prepareChart(
			$this->fetchInput("chartType", ""),
			$this->fetchInput("chart", 0),
		);

		$administeredDate = Carbon::createFromDate(
			$this->fetchInput(
				"administeredDate",
				Carbon::now($invoice->location->tz->php_supported),
			),
		)->toDateString();

		if ($item->itemType->name === "Item Kit") {
			$invoiceItemIds = $this->createInvoiceItemsForItemKit(
				$invoice,
				$item,
				$chart,
				$provider,
				$administeredDate,
			);
		} else {
			$invoiceItemIds = $this->createInvoiceItemForSingleItem(
				$invoice,
				$item,
				$chart,
				$provider,
				$administeredDate,
			);
		}

		$invoice->push();

		$this->reprocessDiscountsTaxesAndTotals($invoice);

		$this->affectedId = $this->args["input"]["invoice"];

		return InvoiceItem::on($this->subdomainName)
			->whereIn("id", $invoiceItemIds)
			->paginate(1);
	}
}
