<?php

namespace App\GraphQL\Requests\Mutations\App\Organization;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\OrganizationSetting;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrganizationSettingsUpdateMutation extends AppHippoMutation
{
	protected $model = OrganizationSetting::class;

	protected $permissionName = "Organizations: Update";

	protected $attributes = [
		"name" => "OrganizationSettingsUpdate",
		"model" => OrganizationSetting::class,
	];

	public function __construct()
	{
		return parent::__construct();
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("OrganizationSettingsInput"),
			],
			"setting_name" => [
				"name" => "setting_name",
				"type" => Type::nonNull(Type::string()),
			],
			"setting_value" => [
				"name" => "setting_value",
				"type" => Type::string(),
			],
		];
	}

	public function validationErrorMessages($args = []): array
	{
		return [];
	}

	public function rules(array $args = []): array
	{
		return [];
	}

	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if ($args["setting_name"] === "returnPolicy") {
			$args["setting_name"] = "return_policy";
		}
		$modelInstance = $this->model
			::on($this->subdomainName)
			->where("setting_name", $args["setting_name"])
			->first();
		if (!$modelInstance) {
			throw new Exception(
				"Cannot update settings for non-existent key: " .
					$args["setting_name"],
			);
		}

		$modelInstance->update(["setting_name" => $args["setting_value"]]);
		$modelInstance->save();
	}
}
