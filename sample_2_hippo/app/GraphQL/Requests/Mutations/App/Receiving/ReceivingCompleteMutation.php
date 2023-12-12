<?php

namespace App\GraphQL\Requests\Mutations\App\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\InventoryStatus;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransactionStatus;
use App\Models\Item;
use App\Models\Receiving;
use App\Models\ReceivingStatus;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Carbon;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingCompleteMutation extends AppHippoMutation
{
	protected $model = Receiving::class;

	protected $permissionName = "Receivings: Update";

	protected $attributes = [
		"name" => "ReceivingComplete",
		"model" => Receiving::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_COMPLETE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" => "The specified receiving does not exist",
			"input.id.required" => "An receiving must be provided",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingCompleteInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @throws SubdomainNotConfiguredException|Exception
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

		/** @var Receiving $receivingToComplete */
		$receivingToComplete = Receiving::on($this->subdomainName)
			->where("id", $this->args["input"]["id"])
			->firstOrFail();

		if ($receivingToComplete->receivingStatus->name !== "Open") {
			throw new Exception(
				"This receiving is no longer open",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		if (!isset($receivingToComplete->supplier_id)) {
			throw new Exception(
				"A supplier must be provided before a receiving can be completed",
				HippoGraphQLErrorCodes::RECEIVING_COMPLETE_NO_SUPPLIER,
			);
		}

		/** @var ReceivingStatus $completeStatus */
		$completeStatus = ReceivingStatus::on($this->subdomainName)
			->where("name", "Complete")
			->firstOrFail();

		$receivingToComplete->update([
			"status_id" => $completeStatus->id,
			"received_at" => Carbon::now(),
			"active" => 0,
		]);

		/** @var InventoryStatus $inventoryCompleteStatus */
		$inventoryCompleteStatus = InventoryStatus::on($this->subdomainName)
			->where("name", "Complete")
			->firstOrFail();

		/** @var InventoryTransactionStatus $inventoryTransactionStatus */
		$inventoryTransactionStatus = InventoryTransactionStatus::on(
			$this->subdomainName,
		)
			->where("name", "Complete")
			->firstOrFail();

		foreach ($receivingToComplete->receivingItems as $receivingItem) {
			foreach ($receivingItem->inventory as $inventory) {
				$inventory->status_id = $inventoryCompleteStatus->id;
				$inventory->save();
				/** @var Item $item */
				$item = Item::on($this->subdomainName)->findOrFail(
					$receivingItem->item_id,
				);
				if ($item->markup_percentage > 0.0) {
					if ($receivingItem->cost_price > $item->cost_price) {
						$item->cost_price = $receivingItem->cost_price;
						$item->unit_price =
							$item->cost_price *
							(1 + $item->markup_percentage / 100);
						$item->save();
					}
				}

				InventoryTransaction::on($this->subdomainName)->create([
					"inventory_id" => $inventory->id,
					"user_id" => $receivingToComplete->user->id,
					"status_id" => $inventoryTransactionStatus->id,
					"quantity" => $inventory->starting_quantity,
					"comment" => "Initial Receiving",
				]);
			}
		}

		$this->affectedId = $this->args["input"]["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->args["input"]["id"])
			->paginate(1);
	}
}
