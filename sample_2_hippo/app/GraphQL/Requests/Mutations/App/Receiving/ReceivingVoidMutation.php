<?php

namespace App\GraphQL\Requests\Mutations\App\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\InventoryStatus;
use App\Models\Receiving;
use App\Models\ReceivingStatus;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingVoidMutation extends AppHippoMutation
{
	protected $model = Receiving::class;

	protected $permissionName = "Receivings: Delete";

	protected $attributes = [
		"name" => "ReceivingVoid",
		"model" => Receiving::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_VOID;

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
				"type" => GraphQL::type("ReceivingVoidInput"),
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
		/** @var Receiving $receiving */
		$receiving = Receiving::on($this->subdomainName)->findOrFail(
			$this->args["input"]["id"],
		);

		if ($receiving->receivingStatus->name !== "Open") {
			throw new Exception(
				"Receivings that are not open cannot be voided",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		/** @var InventoryStatus $inventoryVoidedStatus */
		$inventoryVoidedStatus = InventoryStatus::on($this->subdomainName)
			->where("name", "Voided")
			->firstOrFail();

		foreach ($receiving->receivingItems as $receivingItem) {
			foreach ($receivingItem->inventory as $inventory) {
				$inventory->status_id = $inventoryVoidedStatus->id;
				$inventory->save();
				$inventory->delete();
			}

			$receivingItem->delete();
		}

		/** @var ReceivingStatus $voidedReceivingStatus */
		$voidedReceivingStatus = ReceivingStatus::on($this->subdomainName)
			->where("name", "Voided")
			->firstOrFail();

		$receiving->status_id = $voidedReceivingStatus->id;
		$receiving->active = 0;
		$receiving->save();
		$receiving->delete();

		$this->affectedId = $receiving->id;

		return $this->model
			::on($this->subdomainName)
			->where($receiving->getPrimaryKey(), $receiving->id)
			//			->onlyTrashed() todo deleted record will be never found
			->paginate(1);
	}
}
