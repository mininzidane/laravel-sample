<?php

namespace App\GraphQL\Requests\Mutations\App\ReceivingItem;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\ReceivingItem;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingItemUpdateMutation extends AppHippoMutation
{
	protected $model = ReceivingItem::class;

	protected $permissionName = "Receiving Items: Update";

	protected $attributes = [
		"name" => "ReceivingItemUpdate",
		"model" => ReceivingItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_ITEM_DELETE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" => "The specified receiving item does not exist",
			"input.id.required" => "Please select an Receiving Item to update",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingItemUpdateInput"),
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
		$receivingItemToUpdate = ReceivingItem::on(
			$this->subdomainName,
		)->findOrFail($this->args["input"]["id"]);

		if (
			$receivingItemToUpdate->receiving->receivingStatus->name !== "Open"
		) {
			throw new Exception(
				"The associated receiving is no longer open",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		$inventoryDetails = [];

		$inventoryDetails["starting_quantity"] =
			$this->args["input"]["quantity"];
		$inventoryDetails["remaining_quantity"] =
			$this->args["input"]["quantity"];

		if (array_key_exists("lotNumber", $this->args["input"])) {
			$inventoryDetails["lot_number"] = $this->args["input"]["lotNumber"];
		}

		if (array_key_exists("serialNumber", $this->args["input"])) {
			$inventoryDetails["serial_number"] =
				$this->args["input"]["serialNumber"];
		}

		if (array_key_exists("expirationDate", $this->args["input"])) {
			$inventoryDetails["expiration_date"] = Carbon::parse(
				$this->args["input"]["expirationDate"],
			);
		}

		$receivingItemToUpdate->fill($this->args["input"]);

		$receivingItemToUpdate->save();

		$inventoryToUpdate = $receivingItemToUpdate->inventory()->first();

		$inventoryToUpdate->fill($inventoryDetails);
		$inventoryToUpdate->save();

		$this->affectedId = $receivingItemToUpdate->id;

		return $this->model
			::on($this->subdomainName)
			->where(
				$receivingItemToUpdate->getPrimaryKey(),
				$receivingItemToUpdate->id,
			)
			->paginate(1);
	}
}
