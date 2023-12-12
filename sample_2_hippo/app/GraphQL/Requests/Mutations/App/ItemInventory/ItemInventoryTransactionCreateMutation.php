<?php

namespace App\GraphQL\Requests\Mutations\App\ItemInventory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Closure;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;

class ItemInventoryTransactionCreateMutation extends AppHippoMutation
{
	protected $model = InventoryTransaction::class;

	//bob nbm Permissions for inventory
	protected $permissionName = "Inventory Transactions: Create";

	protected $attributes = [
		"name" => "ItemInventoryTransactionCreateMutation",
	];

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InventoryTransactionCreateInput"),
			],
		];
	}

	/**
	 * @return LengthAwarePaginator
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectedFields
	) {
		if (!array_key_exists("user_id", $this->args["input"])) {
			$userId = Auth::guard("api-subdomain-passport")->user()->id;
			$this->args["input"]["user_id"] = $userId;
		}

		/** @var Inventory $inventory */
		$inventory = Inventory::on($this->subdomainName)->findOrFail(
			$this->args["input"]["inventory_id"],
		);

		/** @var InventoryTransaction $transaction */
		$transaction = InventoryTransaction::on($this->subdomainName)->create(
			$this->args["input"],
		);

		$inventory->increment("remaining_quantity", $transaction->quantity);

		return Inventory::on($this->subdomainName)
			->where("id", $inventory->id)
			->paginate(1);
	}
}
