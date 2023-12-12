<?php

namespace App\GraphQL\Requests\Mutations\App;

use App\Models\User;
use Closure;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Auth\Authenticatable;

class UserMutation extends AppHippoMutation
{
	protected $model = User::class;

	protected $permissionName = "Users: Update";

	protected $attributes = [
		"name" => "UserUpdate",
	];

	public function rules(array $args = []): array
	{
		return [
			"lastLocation" => ["string", "nullable"],
			"firstName" => ["string", "nullable"],
			"clientedUsername" => ["string", "nullable"],
			"clientedPassword" => ["string", "nullable"],
		];
	}

	public function checkPermissions(Authenticatable $user, array $args = null)
	{
		if (!array_key_exists("id", $args)) {
			$args["id"] = $this->guard()->user()->id;
		}

		if ($user->id === intval($args["id"])) {
			return $user->hasPermissionTo("Users: Update Self");
		} else {
			return $user->hasPermissionTo("Users: Update");
		}
	}

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if (!array_key_exists("id", $args)) {
			$args["id"] = $this->guard()->user()->id;
		}

		$this->prepareResolve($args);

		$modelInstance = $this->model
			::on($this->subdomainName)
			->findOrFail($args["id"]);

		$standardTypes = array_keys(Type::getStandardTypes());

		$allowedFields = $this->getFields();

		foreach ($allowedFields as $allowedFieldKey => $allowedField) {
			if (!array_key_exists($allowedFieldKey, $args)) {
				continue;
			}

			$type = is_array($allowedField)
				? $allowedField["type"]
				: $allowedField->type;

			$originalFieldKey = $allowedFieldKey;

			if (in_array($type, $standardTypes)) {
				if (isset($allowedField["alias"])) {
					$allowedFieldKey = $allowedField["alias"];
				}

				$modelInstance->$allowedFieldKey = $args[$originalFieldKey];

				continue;
			}

			/*
			 * If the field has at least one relationship restriction, verify that the restriction is met
			 * before saving updated relationship
			 */
			if (sizeof($allowedField->restrictions["in"]) > 0) {
				foreach (
					$allowedField->restrictions["in"]
					as $relationRestriction
				) {
					$allowedModels = array_column(
						$modelInstance->$relationRestriction->toArray(),
						"id",
					);

					if (!in_array($args[$allowedFieldKey], $allowedModels)) {
						throw new Exception(
							"The selected " .
								$allowedFieldKey .
								": " .
								$args[$allowedFieldKey] .
								" is NOT valid",
						);
					}
				}
			}

			$relatedGraphQLType = "App\GraphQL\Types\\" . $type . "GraphQLType";
			$relatedModel = (new $relatedGraphQLType())->getModel();

			$newModel = $relatedModel
				::on($this->subdomainName)
				->findOrFail($args[$allowedFieldKey]);

			$modelInstance->$allowedFieldKey()->dissociate();

			$modelInstance->$allowedFieldKey()->associate($newModel);
		}

		$modelInstance->save();

		return $this->model
			::on($this->subdomainName)
			->where("id", $args["id"])
			->paginate(1);
	}
}
