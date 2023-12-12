<?php

namespace App\GraphQL\Requests\Mutations\App\Organization;

use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Organization;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrganizationUpdateMutation extends AppHippoMutation
{
	protected $model = Organization::class;

	protected $permissionName = "Organizations: Update";

	protected $attributes = [
		"name" => "OrganizationUpdate",
		"model" => Organization::class,
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
				"type" => Type::string(),
			],
			"input" => [
				"type" => GraphQL::type("OrganizationInput"),
			],
		];
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "Organization name is required",
			"input.ein.required" => "EIN is required",
			"input.units.required" => "Measurement units are required",
			"input.currencySymbol.required" => "Currency is required",
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"input.name" => ["required"],
			"input.ein" => ["required"],
			"input.units" => ["required"],
			"input.currencySymbol" => ["required"],
		];
	}

	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$modelInstance = $this->model
			::on($this->subdomainName)
			->find($args["id"]);
		if (!$modelInstance) {
			throw new Exception(
				"Cannot update non-existent organization: " . $args["id"],
			);
		}

		$modelInstance->fill($args["input"]);
		$modelInstance->save();

		$this->affectedId = $args["id"];

		/*---update settings----*/
		if (isset($args["input"]["vcp_active"])) {
			$modelInstance->vcp_active = $args["input"]["vcp_active"];
		}
		if (isset($args["input"]["vcp_username"])) {
			$modelInstance->vcp_username = $args["input"]["vcp_username"];
		}

		if (
			isset($args["input"]["vcp_password"]) &&
			array_key_exists("vcp_password_changed", $args["input"]) &&
			$args["input"]["vcp_password_changed"]
		) {
			$modelInstance->vcp_password = $args["input"]["vcp_password"];
		}
		if (isset($args["input"]["payment_info"])) {
			$modelInstance->payment_info = $args["input"]["payment_info"];
		}
		if (isset($args["input"]["return_policy"])) {
			$modelInstance->return_policy = $args["input"]["return_policy"];
		}
		if (isset($args["input"]["estimate_statement"])) {
			$modelInstance->estimate_statement =
				$args["input"]["estimate_statement"];
		}
		if (isset($args["input"]["idexx_active"])) {
			$modelInstance->idexx_active = $args["input"]["idexx_active"];
		}
		if (isset($args["input"]["idexx_username"])) {
			$modelInstance->idexx_username = $args["input"]["idexx_username"];
		}
		if (
			isset($args["input"]["idexx_password"]) &&
			array_key_exists("idexx_password_changed", $args["input"]) &&
			$args["input"]["idexx_password_changed"]
		) {
			$modelInstance->idexx_password = $args["input"]["idexx_password"];
		}
		/*--------------------*/

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $args["id"])
			->paginate(1);
	}
}
