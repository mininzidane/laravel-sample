<?php

namespace App\GraphQL\Requests\Mutations\App\ReceivingItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\InventoryStatus;
use App\Models\Item;
use App\Models\Receiving;
use App\Models\ReceivingItem;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingItemCreateMutation extends AppHippoMutation
{
	protected $model = ReceivingItem::class;

	protected $permissionName = "Receiving Items: Create";

	protected $attributes = [
		"name" => "ReceivingItemCreate",
		"model" => ReceivingItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_ITEM_CREATE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.receiving.exists" => "Please select a valid receiving",
			"input.item.exists" => "Please select a valid item to add",
			"input.quantity.min" => "Please enter a quantity greater than zero",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingItemCreateInput"),
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

		$item = Item::on($this->subdomainName)->findOrFail(
			$this->args["input"]["item"],
		);

		$receiving = Receiving::on($this->subdomainName)->findOrFail(
			$this->args["input"]["receiving"],
		);

		if ($receiving->receivingStatus->name !== "Open") {
			throw new Exception(
				"The associated receiving is no longer open",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		$currentMaxLine = $receiving->receivingItems->max("line");
		$newLineNumber = $currentMaxLine > 0 ? $currentMaxLine + 1 : 1;

		$receivingItem = $receiving->receivingItems()->create([
			"item_id" => $item->id,
			"quantity" => $this->args["input"]["quantity"],
			"comment" => "",
			"cost_price" => $item->cost_price,
			"discount_percentage" => 0,
			"unit_price" => $item->unit_price,
			"line" => $newLineNumber,
		]);

		$pendingInventoryStatus = InventoryStatus::on($this->subdomainName)
			->where("name", "Pending")
			->firstOrFail();

		$inventoryDetails = [
			"location_id" => $receiving->location_id,
			"status_id" => $pendingInventoryStatus->id,
			"item_id" => $item->id,
			"receiving_item_id" => $receivingItem->id,
		];

		if (array_key_exists("quantity", $this->args["input"])) {
			$inventoryDetails["starting_quantity"] =
				$this->args["input"]["quantity"];
			$inventoryDetails["remaining_quantity"] =
				$this->args["input"]["quantity"];
		}

		if (array_key_exists("lotNumber", $this->args["input"])) {
			$inventoryDetails["lot_number"] = $this->args["input"]["lotNumber"];
		}

		if (array_key_exists("serialNumber", $this->args["input"])) {
			$inventoryDetails["serial_number"] =
				$this->args["input"]["serialNumber"];
		}

		if (array_key_exists("expirationDate", $this->args["input"])) {
			$inventoryDetails["expiration_date"] = Carbon::parse(
				$this->fetchInput("expirationDate"),
			);
		}

		$receivingItem->inventory()->create($inventoryDetails);

		$this->affectedId = $receivingItem->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $receivingItem->id)
			->paginate(1);
	}
}
