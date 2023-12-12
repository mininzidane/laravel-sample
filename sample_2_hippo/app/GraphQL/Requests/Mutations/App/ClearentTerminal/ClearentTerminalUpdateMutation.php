<?php

namespace App\GraphQL\Requests\Mutations\App\ClearentTerminal;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\ClearentTerminal;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ClearentTerminalUpdateMutation extends AppHippoMutation
{
	protected $model = ClearentTerminal::class;

	protected $permissionName = "Clearent Terminals: Update";

	protected $attributes = [
		"name" => "ClearentTerminalUpdate",
		"model" => ClearentTerminal::class,
	];

	protected $actionId = HippoGraphQLActionCodes::CLEARENT_TERMINAL_UPDATE;

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.id.exists" =>
				"The specified clearent terminal does not exist",
			"input.location.exists" => "The specified location does not exist",
			"input.id.required" =>
				"Please select an Clearent Terminal to update",
			"input.location.required" =>
				"Please select a location for this item",
		];
	}

	public function args(): array
	{
		return [
			"input" => [
				"type" => GraphQL::type("ClearentTerminalUpdateInput"),
			],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$invoiceItemToUpdate = ClearentTerminal::on(
			$this->subdomainName,
		)->findOrFail($this->args["input"]["id"]);

		$invoiceItemToUpdate->fill($this->args["input"]);

		$invoiceItemToUpdate->save();

		$this->affectedId = $invoiceItemToUpdate->id;

		return $this->model
			::on($this->subdomainName)
			->where(
				$invoiceItemToUpdate->getPrimaryKey(),
				$invoiceItemToUpdate->id,
			)
			->paginate(1);
	}
}
