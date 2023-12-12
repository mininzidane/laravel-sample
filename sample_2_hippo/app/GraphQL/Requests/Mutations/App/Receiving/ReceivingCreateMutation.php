<?php

namespace App\GraphQL\Requests\Mutations\App\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Receiving;
use App\Models\ReceivingStatus;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingCreateMutation extends AppHippoMutation
{
	protected $model = Receiving::class;

	protected $permissionName = "Receivings: Create";

	protected $attributes = [
		"name" => "ReceivingCreate",
		"model" => Receiving::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_CREATE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.user.exists" => "Please select a valid user",
			"input.location.exists" => "Please select a valid location",
			"input.location.required" =>
				"A location must be provided for this request",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingCreateInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
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

		/** @var ReceivingStatus $newReceivingStatus */
		$newReceivingStatus = ReceivingStatus::on($this->subdomainName)
			->where("name", "Open")
			->firstOrFail();

		$this->args["input"]["status_id"] = $newReceivingStatus->id;

		if (!array_key_exists("user_id", $this->args["input"])) {
			$userId = Auth::guard("api-subdomain-passport")->user()->id;
			$this->args["input"]["user_id"] = $userId;
		}

		$this->affectedId = $modelInstance->create($this->args["input"])->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $this->affectedId)
			->paginate(1);
	}
}
