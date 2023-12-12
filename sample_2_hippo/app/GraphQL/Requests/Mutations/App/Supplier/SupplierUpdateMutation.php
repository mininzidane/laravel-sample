<?php

namespace App\GraphQL\Requests\Mutations\App\Supplier;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Supplier;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SupplierUpdateMutation extends AppHippoMutation
{
	protected $model = Supplier::class;

	protected $permissionName = "Suppliers: Update";

	protected $attributes = [
		"name" => "SupplierUpdate",
		"model" => Supplier::class,
	];

	protected $actionId = HippoGraphQLActionCodes::SUPPLIER_UPDATE;

	public function __construct()
	{
		return parent::__construct();
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.companyName.required" => "Supplier name is required",
			"input.companyName.unique" =>
				"This Supplier name is already in use",
		];
	}

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::int(),
			],
			"input" => [
				"type" => GraphQL::type("SupplierUpdateInput"),
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
		$modelInstance = $this->model
			::on($this->subdomainName)
			->findorFail($this->args["id"]);

		$id = $modelInstance->fill($this->args["input"])->id;
		$modelInstance->save();

		// Does the updated name exists as a deleted record?
		$existing = $this->model
			::on($this->subdomainName)
			->where("company_name", $args["input"]["company_name"])
			->onlyTrashed()
			->first();

		// It exists so modify deleted so new name can be used (append its id)
		if ($existing) {
			$existing->update([
				"company_name" => $existing->company_name . "_" . $existing->id,
			]);
		}

		$modelInstance->update($args["input"]);
		$this->affectedId = $args["id"];

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
