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
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingItemDeleteMutation extends AppHippoMutation
{
	protected $model = ReceivingItem::class;

	protected $permissionName = "Receiving Items: Delete";

	protected $attributes = [
		"name" => "ReceivingItemDelete",
		"model" => ReceivingItem::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_ITEM_DELETE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.receivingItem.exists" => "Please select a valid receiving",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingItemDeleteInput"),
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
		$receivingItemToDelete = $this->model
			::on($this->subdomainName)
			->findOrFail($this->args["input"]["receivingItem"]);

		if (
			$receivingItemToDelete->receiving->receivingStatus->name !== "Open"
		) {
			throw new Exception(
				"The associated receiving is no longer open",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		$receivingItemToDelete->line = -1 * $receivingItemToDelete->id;
		$receivingItemToDelete->save();

		$receivingItemToDelete->inventory()->delete();

		$receivingItemToDelete->delete();

		$this->affectedId = $this->args["input"]["receivingItem"];

		return $this->model
			::on($this->subdomainName)
			->where("id", $this->args["input"]["receivingItem"])
			->paginate(1);
	}
}
