<?php

namespace App\GraphQL\Requests\Mutations\App\ItemInventory;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Inventory;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Closure;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;

class ItemInventoryNewLineCreateMutation extends AppHippoMutation
{
	protected $model = Inventory::class;

	protected $permissionName = "Inventory: Create";

	protected $attributes = [
		"name" => "ItemInventoryNewLineCreateMutation",
	];

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("InventoryNewLineInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return LengthAwarePaginator|null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$this->prepareResolve($args);

		/** @var Inventory $modelInstance */
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		if (!array_key_exists("opened_at", $this->args["input"])) {
			$date = date("Y-m-d H:i:s");
			$args["input"]["opened_at"] = $date;
		}

		$id = $modelInstance->create($args["input"])->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $id)
			->paginate(1);
	}
}
