<?php

namespace App\GraphQL\Requests\Mutations\App\Supplier;

use Closure;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use App\GraphQL\HippoGraphQLActionCodes;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;

class SupplierCreateMutation extends AppHippoMutation
{
	protected $model = Supplier::class;

	protected $permissionName = "Suppliers: Create";

	protected $attributes = [
		"name" => "supplierCreate",
		"model" => Supplier::class,
	];

	protected $actionId = HippoGraphQLActionCodes::SUPPLIER_CREATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.companyName.required" => "The Supplier name is required",
			"input.companyName.unique" =>
				"This Supplier name is already in use",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("SupplierCreateInput"),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.companyName" => [
				"required",
				"max:255",
				Rule::unique(
					request()->header("Subdomain") . ".suppliers",
					"company_name",
				)->where(function ($query) use ($args) {
					return $query
						->where("company_name", $args["input"]["companyName"])
						->where("deleted_at", null);
				}),
			],
		];
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
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$supplier = $modelInstance
			->withTrashed()
			->updateOrCreate($args["input"], $args["input"]);

		if ($supplier->trashed()) {
			$supplier->restore();
		}

		$this->affectedId = $supplier->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
