<?php

namespace App\GraphQL\Requests\Mutations\App\Item;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Item;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class ItemDeleteMutation extends AppHippoMutation
{
	protected $model = Item::class;

	protected $permissionName = "Items: Delete";

	protected $attributes = [
		"name" => "ItemDelete",
		"model" => Item::class,
	];

	protected $actionId = HippoGraphQLActionCodes::ITEM_DELETE;

	public function __construct()
	{
		return parent::__construct();
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return mixed |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$modelInstance = $this->model
			::on($this->subdomainName)
			->findOrFail($args["id"]);
		$modelInstance->setConnection($this->subdomainName);

		$modelInstance->delete();

		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
