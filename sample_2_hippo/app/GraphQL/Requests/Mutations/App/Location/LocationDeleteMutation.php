<?php

namespace App\GraphQL\Requests\Mutations\App\Location;

use App\Models\Location;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class LocationDeleteMutation extends LocationMutation
{
	protected $model = Location::class;

	protected $permissionName = "Locations: Delete";

	protected $attributes = [
		"name" => "LocationDelete",
		"model" => Location::class,
	];

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::string(),
			],
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"id" => ["required"],
		];
	}

	public function resolveTransaction($root, $args, $context)
	{
		$modelInstance = $this->model
			::on($this->subdomainName)
			->find($args["id"]);
		if (!$modelInstance) {
			throw new Exception(
				"Cannot delete non-existent location " . $args["id"],
			);
		}

		$modelInstance->delete();
		$this->affectedId = $args["id"];

		return $modelInstance->paginate(1);
	}
}
