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
use Rebing\GraphQL\Support\Facades\GraphQL;

class ReceivingSaveDetailsMutation extends AppHippoMutation
{
	protected $model = Receiving::class;

	protected $permissionName = "Receivings: Update";

	protected $attributes = [
		"name" => "ReceivingSaveDetails",
		"model" => Receiving::class,
	];

	protected $actionId = HippoGraphQLActionCodes::RECEIVING_SAVE_DETAILS;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" => "The specified receiving does not exist",
			"input.id.required" => "An receiving must be provided",
			"input.supplier.exists" => "The specified receiving does not exist",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ReceivingSaveDetailsInput"),
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

		$updatedReceivingDetails = [];

		if (array_key_exists("comment", $this->args["input"])) {
			$updatedReceivingDetails["comment"] =
				$this->args["input"]["comment"];
		}

		if (array_key_exists("supplier", $this->args["input"])) {
			$updatedReceivingDetails["supplier_id"] =
				$this->args["input"]["supplier"];
		}

		/** @var Receiving $receivingToUpdate */
		$receivingToUpdate = $this->model
			::on($this->subdomainName)
			->where("id", $this->args["input"]["receiving"])
			->firstOrFail();

		if ($receivingToUpdate->receivingStatus->name !== "Open") {
			throw new Exception(
				"This receiving is no longer open",
				HippoGraphQLErrorCodes::RECEIVING_NOT_OPEN,
			);
		}

		$receivingToUpdate->update($updatedReceivingDetails);

		$this->affectedId = $receivingToUpdate->id;

		return $this->model
			::on($this->subdomainName)
			->where($modelInstance->getPrimaryKey(), $receivingToUpdate->id)
			->paginate(1);
	}
}
