<?php

namespace App\GraphQL\Requests\Mutations\App\Tax;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Tax;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;

class TaxDeleteMutation extends AppHippoMutation
{
	protected $model = Tax::class;

	protected $permissionName = "Taxes: Delete";

	protected $attributes = [
		"name" => "TaxDelete",
		"model" => Tax::class,
	];

	protected $actionId = HippoGraphQLActionCodes::TAX_DELETE;

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
