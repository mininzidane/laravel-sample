<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

abstract class HippoGraphQLType extends GraphQLType
{
	public static $graphQLType = "HippoChart";

	public static function getGraphQLTypeName()
	{
		return static::$graphQLType;
	}

	public function getModel()
	{
		return $this->attributes["model"];
	}

	public function fields(): array
	{
		$fields = $this->columns();

		$model = new $this->attributes["model"]();

		if ($model->timestamps) {
			$timestampFields = [
				"createdAt" => [
					"type" => Type::string(),
					"description" => "The time this was created",
					"alias" => $model->getCreatedAtColumn(),
				],
				"updatedAt" => [
					"type" => Type::string(),
					"description" => "The time this was last updated",
					"alias" => $model->getUpdatedAtColumn(),
				],
			];

			if (is_array($fields)) {
				$fields = array_merge($fields, $timestampFields);
			}
		}

		if ($model->soft_deleting) {
			$softDeleteFields = [
				"deletedAt" => [
					"type" => Type::string(),
					"description" => "The time this was removed",
					"alias" => "deleted_at",
				],
			];

			if (is_array($fields)) {
				$fields = array_merge($fields, $softDeleteFields);
			}
		}

		return $fields;
	}
}
