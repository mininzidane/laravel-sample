<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use NumberFormatter;
use Config;
use Postmark\PostmarkClient;

class InvoiceController extends Controller
{
	public function __construct()
	{
	}

	public function sendReceipt(Request $request, $invoiceId = null)
	{
		$subdomain = $request->header("Subdomain");

		$this->createSubdomainConnection($subdomain);

		$invoiceCollection = Invoice::on($subdomain)
			->with([
				"owner",
				"location",
				"patient",
				"invoiceItems",
				"invoiceItems.invoiceItemTaxes",
				"invoicePayments" => function ($query) {
					$query->whereNotNull("amount_applied");
				},
				"invoicePayments.payment",
				"invoicePayments.payment.paymentMethod",
			])
			->where("id", $invoiceId)
			->get();

		$postmark = new PostmarkClient(Config::get("services.postmark.token"));

		$data = $this->getTemplateData($invoiceCollection)[0];
		$invoice = $invoiceCollection->first();
		$emailIdentity = $this->getEmailIdentity($invoice, $subdomain);

		$emailResult = $postmark->sendEmailWithTemplate(
			$emailIdentity,
			$invoice->owner->email,
			"hm-invoice-2",
			$data,
			true,
			"Invoice",
			null,
			$this->getReplyTo($invoice),
			null,
			null,
			null,
			null,
			null,
			[
				"Subdomain" => $subdomain,
				"OrgID" => $invoice->location->organization->id,
				"Location" => $invoice->location->name,
			],
		);

		$jsonBody = [
			"ToAddress" => $invoice->owner->email,
			"OrgID" => strval($invoice->location->organization->id),
			"Subdomain" => $subdomain,
			"FromName" => $invoice->location->name,
			"Identity" => $emailIdentity,
			"Template" => "hm-invoice-2",
			"Data" => $data,
		];

		return response($jsonBody);
	}

	public function sendMultiInvoiceReceipt(Request $request)
	{
		$subdomain = $request->header("Subdomain");
		$this->createSubdomainConnection($subdomain);

		$invoices = Invoice::on($subdomain)
			->with([
				"owner",
				"location",
				"patient",
				"invoiceItems",
				"invoiceItems.invoiceItemTaxes",
				"invoicePayments" => function ($query) {
					$query->whereNotNull("amount_applied");
				},
				"invoicePayments.payment",
				"invoicePayments.payment.paymentMethod",
			])
			->whereIn("id", explode(",", $request->invoiceIds))
			->get();

		$postmark = new PostmarkClient(Config::get("services.postmark.token"));

		$locationName = str_replace(
			",",
			"",
			$invoices->first()->location->name,
		);
		$ownerEmails = implode(
			",",
			$invoices
				->map(function ($invoice) {
					return $invoice->owner->email;
				})
				->unique()
				->toArray(),
		);

		$organizationId = $invoices->first()->location->organization->id;

		$data["invoices"] = $this->getTemplateData($invoices);

		$emailIdentity = $this->getEmailIdentity(
			$invoices->first(),
			$subdomain,
		);
		$data[
			"subject"
		] = "Multi-Invoice from {$invoices->first()->location->name}";

		$emailResult = $postmark->sendEmailWithTemplate(
			$emailIdentity,
			$ownerEmails,
			"hm-multi-invoice",
			$data,
			true,
			"MultiInvoice",
			null,
			$this->getReplyTo($invoices->first()),
			null,
			null,
			null,
			null,
			null,
			[
				"Subdomain" => $subdomain,
				"OrgID" => $organizationId,
				"Location" => $locationName,
			],
		);

		$jsonBody = [
			"ToAddress" => $ownerEmails,
			"OrgID" => strval($organizationId),
			"Subdomain" => $subdomain,
			"FromName" => $locationName,
			"Identity" => $emailIdentity,
			"Template" => "hm-multi-invoice",
			"Data" => $data,
		];

		return response($jsonBody);
	}

	private function getTemplateData(Collection $invoices): array
	{
		$data = [];
		foreach ($invoices as $key => $invoice) {
			$data[$key] = [
				"location" => [
					"name" => $invoice->location->name,
					"address1" => $invoice->location->address1,
					"address2" => $invoice->location->address2,
					"city" => $invoice->location->city,
					"state" => $invoice->location->subregion
						? $invoice->location->subregion->name
						: "",
					"zip_code" => $invoice->location->zip,
					"email" => $invoice->location->email,
					"phone" => $invoice->location->phone,
					"logo" => $invoice->location->imageUrl,
				],
				"owner" => [
					"first_name" => $invoice->owner->first_name,
					"last_name" => $invoice->owner->last_name,
					"address1" => $invoice->owner->address1,
					"address2" => $invoice->owner->address2,
					"city" => $invoice->owner->city,
					"state" => $invoice->owner->subregion->name,
					"zip_code" => $invoice->owner->zip,
				],
				"patient" => [
					"name" => $invoice->patient->name,
				],
				"invoice" => [
					"number" => strval($invoice->id),
					"type" => $invoice->type,
					"date" => (new Carbon($invoice->created_at))
						->setTimezone($invoice->location->tz->php_supported)
						->isoFormat("MMMM Do YYYY"),
					"total" => (new NumberFormatter(
						"en_US",
						NumberFormatter::CURRENCY,
					))->formatCurrency($invoice->total, "USD"),
					"balance" => (new NumberFormatter(
						"en_US",
						NumberFormatter::CURRENCY,
					))->formatCurrency($invoice->amountDue, "USD"),
					"message_type" => $invoice->emailMessageType,
					"message" => $invoice->emailMessage ?: "",
					"comments" => $invoice->comment,
				],
				"items" => [],
				"payments" => [],
				"reminders" => [],
			];

			$hiddenItemKitItemIds = [];
			$taxes = [];
			foreach ($invoice->invoiceItems as $invoiceItem) {
				$price = 0;

				if ($invoiceItem->is_single_line_kit) {
					foreach ($invoice->invoiceItems as $item) {
						if ($item->belongs_to_kit_id === $invoiceItem->id) {
							$price += $item->total + $item->discount_amount;
							$hiddenItemKitItemIds[] = $item->id;
						}
					}
				} elseif ($invoiceItem->itemType->name === "Discount Code") {
					$price = $invoiceItem->price * -1;
				} else {
					$price =
						$invoiceItem->total + $invoiceItem->discount_amount;
				}

				if (
					array_search($invoiceItem->id, $hiddenItemKitItemIds) ===
					false
				) {
					$data[$key]["items"][] = [
						"name" => $invoiceItem->name,
						"description" => $invoiceItem->description,
						"provider" => $invoiceItem->provider
							? $invoiceItem->provider->fullName
							: "",
						"quantity" => $invoiceItem->quantity,
						"price" => (new NumberFormatter(
							"en_US",
							NumberFormatter::CURRENCY,
						))->formatCurrency($price, "USD"),
					];
				}

				foreach ($invoiceItem->invoiceItemTaxes as $tax) {
					$taxes[] = [
						"tax_id" => $tax["tax_id"],
						"name" => $tax["name"],
						"percent" => $tax["percent"],
						"amount" => $tax["amount"],
					];
				}
			}

			// array_values() used to re-key the array starting at 0, Postmark doesn't like it otherwise
			$data[$key]["taxes"] = array_values(
				collect($taxes)
					->groupBy("tax_id")
					->map(function ($taxGroup) {
						return [
							"tax_id" => $taxGroup[0]["tax_id"],
							"name" => $taxGroup[0]["name"],
							"percent" => $taxGroup[0]["percent"],
							"total" => (new NumberFormatter(
								"en_US",
								NumberFormatter::CURRENCY,
							))->formatCurrency(
								$taxGroup->reduce(function ($carry, $tax) {
									return $carry + $tax["amount"];
								}),
								"USD", // TODO what about other currencies?
							),
						];
					})
					->all(),
			);

			foreach ($invoice->invoicePayments as $invoicePayment) {
				$data[$key]["payments"][] = [
					"method" => $invoicePayment->payment->paymentMethod->name,
					"date" => (new Carbon(
						$invoicePayment->payment->received_at,
					))->isoFormat("MMMM Do YYYY"),
					"amount" => (new NumberFormatter(
						"en_US",
						NumberFormatter::CURRENCY,
					))->formatCurrency($invoicePayment->amount_applied, "USD"),
				];
			}

			foreach ($invoice->patient->reminders as $reminder) {
				$data[$key]["reminders"][] = [
					"description" => $reminder->description,
					"date" => (new Carbon($reminder->due_date))->isoFormat(
						"MMMM Do YYYY",
					),
				];
			}
		}
		return $data;
	}

	private function getEmailIdentity(
		Invoice $invoice,
		string $subdomain
	): string {
		$locationName = str_replace(",", "", $invoice->location->name);

		if (!$invoice->location->email_identity_verified) {
			return "Hippo Manager <info@hippomanager.com>";
		} elseif ($invoice->location->public_domain_email) {
			return "{$locationName} <{$subdomain}@hippo.vet>";
		} else {
			return "{$locationName} <{$invoice->location->email}>";
		}
	}

	private function getReplyTo(Invoice $invoice)
	{
		if ($invoice->location->public_domain_email) {
			return $invoice->location->email;
		}
		return null;
	}
}
