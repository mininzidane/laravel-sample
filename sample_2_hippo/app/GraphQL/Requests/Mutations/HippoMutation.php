<?php

namespace App\GraphQL\Requests\Mutations;

use App\GraphQL\Requests\HippoRequest;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

abstract class HippoMutation extends HippoRequest
{
	protected $model = null;
	protected $args = null;
	protected $argService = null;
	protected $graphQLType = null;
	protected $permissionName = null;

	protected $arguments = [];

	public function args(): array
	{
		$fields = [
			"id" => [
				"name" => "id",
				"type" => Type::string(),
			],
			"subdomain" => [
				"name" => "subdomain",
				"type" => Type::string(),
			],
		];

		$additionalFields = $this->getFields();

		foreach ($additionalFields as $additionalFieldKey => $additionalField) {
			$type = Type::string();

			//			if($additionalField['type'] != 'string') {
			//
			//			}

			$fields[$additionalFieldKey] = [
				"name" => $additionalFieldKey,
				"type" => $type,
			];
		}

		return $fields;
	}

	public function getFields()
	{
		$modelGraphqlType = $this->model::getGraphQLType();

		$columns = (new $modelGraphqlType())->columns();

		$guardedFields = (new $this->model())->getGuarded();

		return array_filter(
			$columns,
			function ($column) use ($guardedFields) {
				return !in_array($column, $guardedFields);
			},
			ARRAY_FILTER_USE_KEY,
		);
	}

	public function resolve(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		if (!array_key_exists("subdomain", $args)) {
			throw new MissingMandatoryParametersException(
				"A subdomain must be specified on the base graphql request",
			);
		}

		if (!array_key_exists("id", $args)) {
			throw new MissingMandatoryParametersException(
				"An id must be provide for this update request",
			);
		}

		$subdomainName = $args["subdomain"];

		$this->connectToSubdomain($subdomainName);

		$modelInstance = $this->model::on($subdomainName)->find($args["id"]);

		if (!$modelInstance) {
			return null;
		}

		$allowedFields = $this->getFields();

		foreach ($allowedFields as $allowedFieldKey => $allowedField) {
			if (!array_key_exists($allowedFieldKey, $args)) {
				continue;
			}

			$fieldKey = $allowedFieldKey;

			if (array_key_exists("alias", $allowedField)) {
				$fieldKey = $allowedField["alias"];
			}

			$modelInstance->$fieldKey = $args[$allowedFieldKey];
		}

		$modelInstance->save();

		return $modelInstance->where("id", $args["id"])->get();
	}
}
