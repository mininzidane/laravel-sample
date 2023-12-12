<?php

namespace App\GraphQL\Requests\Mutations\App\Receiving;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\HippoGraphQLErrorCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Receiving;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingSetActiveMutation extends AppHippoMutation
{
	protected $model = Receiving::class;

	protected $permissionName = "Receivings: Set Active";

	protected $attributes = [
		"name" => "ReceivingSetActive",
		"model" => Receiving::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_SET_ACTIVE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.receivingId.exists" =>
				"The specified receiving does not exist",
			"input.receivingId.required" => "An receiving must be provided",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingSetActiveInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @throws SubdomainNotConfiguredException|Exception
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

		$this->model
			::on($this->subdomainName)
			->where("active", 1)
			->update(["active" => 0]);

		$receivingToSetActive = $this->model
			::on($this->subdomainName)
			->where("id", $this->args["input"]["receivingId"])
			->firstOrFail();

		if ($receivingToSetActive->receivingStatus->name !== "Open") {
			throw new Exception(
				"Completed or Voided receivings cannot be made the active receiving",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		$receivingToSetActive->update(["active" => 1]);

		$this->affectedId = $receivingToSetActive->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $receivingToSetActive->id)
			->paginate(1);
	}
}
