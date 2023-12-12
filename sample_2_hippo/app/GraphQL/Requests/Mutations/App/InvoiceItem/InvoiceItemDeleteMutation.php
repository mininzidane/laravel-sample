<?php

namespace App\GraphQL\Requests\Mutations\App\InvoiceItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\Models\InvoiceItem;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvoiceItemDeleteMutation extends InvoiceItemMutation
{
	protected $model = InvoiceItem::class;

	protected $permissionName = "Invoice Items: Delete";

	protected $attributes = [
		"name" => "InvoiceItemDelete",
		"model" => InvoiceItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::INVOICE_ITEM_DELETE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.invoiceItem.exists" => "Please select a valid invoice",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InvoiceItemDeleteInput"),
			],
		];
	}

	/**
	 * @param string $root
	 * @param Array $args
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
		$invoiceItemId = $this->fetchInput("invoiceItem", null);

		// TODO: update exception
		if (!$invoiceItemId) {
			throw new \Exception("A valid invoice item must be specified");
		}

		$this->deleteInvoiceItem($invoiceItemId);

		$this->affectedId = $invoiceItemId;

		return $this->model
			::on($this->subdomainName)
			->where("id", $invoiceItemId)
			->paginate(1);
	}
}
