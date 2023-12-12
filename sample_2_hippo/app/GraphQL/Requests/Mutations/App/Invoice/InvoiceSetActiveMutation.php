<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\Models\Invoice;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceSetActiveMutation extends InvoiceMutation
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoice: Set Active";

	protected $attributes = [
		"name" => "InvoiceSetActive",
		"model" => Invoice::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_SET_ACTIVE;

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
				"type" => GraphQL::type("InvoiceSetActiveInput"),
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
		$invoiceToSetActive = Invoice::on($this->subdomainName)->findOrFail(
			$this->args["input"]["invoiceId"],
		);

		if ($invoiceToSetActive->invoiceStatus->name === "Complete") {
			throw new \Exception(
				"A completed sale cannot be made the active invoice without reopening the invoice first.",
				HippoGraphQLErrorCodes::INVOICE_SET_ACTIVE_ON_COMPLETED,
			);
		}

		$patientId = $invoiceToSetActive->patient->id;
		$locationId = $invoiceToSetActive->location->id;

		$this->model
			::on($this->subdomainName)
			->where("patient_id", $patientId)
			->where("location_id", $locationId)
			->where("active", 1)
			->update(["active" => 0]);

		$invoiceToSetActive->update(["active" => 1]);

		$this->affectedId = $this->args["input"]["invoiceId"];

		return $this->model
			::on($this->subdomainName)
			->where("id", $this->args["input"]["invoiceId"])
			->paginate(1);
	}
}
