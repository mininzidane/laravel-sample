<?php

namespace App\GraphQL\Requests\Mutations\App\Invoice;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\Owner;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceCreateMutation extends AppHippoMutation
{
	protected $model = Invoice::class;

	protected $permissionName = "Invoices: Create";

	protected $attributes = [
		"name" => "InvoiceCreate",
		"model" => Invoice::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_CREATE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.patient.exists" => "The supplied patient does not exist",
			"input.patient.required" => "A valid patient is required",
			"input.owner.exists" => "The supplied owner does not exist",
			"input.owner.required" => "A valid owner is required",
			"input.location.exists" => "The supplied location does not exist",
			"input.location.required" => "A valid location is required",
			"input.user.exists" => "The supplied user does not exist",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceCreateInput"),
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

		$newInvoiceStatus = InvoiceStatus::on($this->subdomainName)
			->where("name", "Open")
			->firstOrFail();
		$this->args["input"]["status_id"] = $newInvoiceStatus->id;

		if (!array_key_exists("user_id", $this->args["input"])) {
			$userId = Auth::guard("api-subdomain-passport")->user()->id;
			$this->args["input"]["user_id"] = $userId;
		}

		$owner = Owner::on($this->subdomainName)->findOrFail(
			$this->args["input"]["owner_id"],
		);
		$this->args["input"]["is_taxable"] = $owner->taxable;

		$newInvoice = $modelInstance->create($this->args["input"]);
		$this->affectedId = $newInvoice->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
