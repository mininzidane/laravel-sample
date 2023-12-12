<?php

namespace App\GraphQL\Requests\Mutations\App\ItemInventory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Inventory;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Closure;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;

class ItemInventoryIsOpenUpdateMutation extends AppHippoMutation
{
	protected $model = Inventory::class;

	// no permission exists for "Inventory: Update"
	protected $permissionName = "Inventory: Create";

	protected $attributes = [
		"name" => "itemInventoryIsOpenUpdate",
	];

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::int(),
			],
			"isOpen" => [
				"type" => Type::boolean(),
				"alias" => "is_open",
			],
		];
	}

	/**
	 * @param $root
	 * @param array $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return LengthAwarePaginator|null
	 * @throws SubdomainNotConfiguredException|\Exception
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		/** @var Inventory $modelInstance */
		$modelInstance = Inventory::on($this->subdomainName)->find($args["id"]);

		if (!$modelInstance) {
			throw new \Exception(
				"Cannot update non-existent item inventory: " . $args["id"],
			);
		}

		$modelInstance->is_open = $args["is_open"];
		$modelInstance->save();

		$this->affectedId = $args["id"];

		return Inventory::on($this->subdomainName)
			->where("id", $args["id"])
			->paginate(1);
	}
}
