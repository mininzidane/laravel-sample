<?php

namespace App\GraphQL\Requests\Mutations\App\InvoiceItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceItemUpdateMutation extends InvoiceItemMutation
{
	protected $model = InvoiceItem::class;

	protected $permissionName = "Invoice Items: Update";

	protected $attributes = [
		"name" => "InvoiceItemUpdate",
		"model" => InvoiceItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_ITEM_UPDATE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" => "The specified invoice item does not exist",
			"input.provider.exists" => "The specified provider does not exist",
			"input.id.required" => "Please select an Invoice Item to update",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceItemUpdateInput"),
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
		$invoiceItemToUpdate = InvoiceItem::on(
			$this->subdomainName,
		)->findOrFail($this->args["input"]["id"]);
		$invoice = $invoiceItemToUpdate->invoice;
		$requestedQuantity = $this->fetchInput("quantity", null);

		$providerId = $this->fetchInput("provider", null);
		$providerId = $providerId == 0 ? null : $providerId;

		$allowExcessiveQuantity = $this->fetchInput(
			"allowExcessiveQuantity",
			false,
		);
		$chartType = $this->fetchInput("chart_type", "");
		$chartId = $this->fetchInput("chart_id", 0);

		$invoiceItemToUpdate = $this->handleInvoiceItemQuantityModifications(
			$invoiceItemToUpdate,
			$requestedQuantity,
			$allowExcessiveQuantity,
		);
		$invoiceItemToUpdate = $this->handleInvoiceItemChartModifications(
			$invoiceItemToUpdate,
			$chartType,
			$chartId,
		);
		$invoiceItemToUpdate = $this->handleInvoiceItemProviderModifications(
			$invoiceItemToUpdate,
			$providerId,
		);

		if (isset($this->args["input"]["chart_type"])) {
			unset($this->args["input"]["chart_type"]);
		}

		if (isset($this->args["input"]["chart_id"])) {
			unset($this->args["input"]["chart_id"]);
		}

		if (isset($this->args["input"]["quantity"])) {
			unset($this->args["input"]["quantity"]);
		}

		if (isset($this->args["input"]["provider_id"])) {
			unset($this->args["input"]["provider_id"]);
		}

		if ($this->args["input"]["administered_date"]) {
			$this->args["input"]["administered_date"] = Carbon::createFromDate(
				$this->args["input"]["administered_date"],
			)->toDateString();
		}

		$invoiceItemToUpdate->fill($this->args["input"]);
		$invoiceItemToUpdate->save();

		if ($invoiceItemToUpdate->vaccination()->exists()) {
			$invoiceItemToUpdate->vaccination->invoice_id =
				$invoiceItemToUpdate->invoice->id;

			if (isset($this->args["input"]["serial_number"])) {
				$invoiceItemToUpdate->vaccination->serialnumber =
					$this->args["input"]["serial_number"];
			}

			if (isset($this->args["input"]["administered_date"])) {
				$invoiceItemToUpdate->vaccination->administered_date =
					$this->args["input"]["administered_date"];
			}

			$invoiceItemToUpdate->vaccination->save();
		}

		$this->reprocessDiscountsTaxesAndTotals($invoice);

		$this->affectedId = $invoiceItemToUpdate->id;

		return InvoiceItem::on($this->subdomainName)
			->where(
				$invoiceItemToUpdate->getPrimaryKey(),
				$invoiceItemToUpdate->id,
			)
			->paginate(1);
	}
}
