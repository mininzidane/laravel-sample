<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\DefaultResolver;
use GraphQL\Type\Definition\Type;

class DefaultArguments implements Arguments
{
	public static $resolver = DefaultResolver::class;
	public $model = null;

	public function __construct($model = null)
	{
		if ($model) {
			$this->model = $model;
		}
	}

	public static function getResolver()
	{
		return static::$resolver;
	}

	public function getArgs()
	{
		// If the id is not a primary key auto incrementing field, set the primary key field on the model to null
		$defaultArgs = [
			"subdomain" => [
				"name" => "subdomain",
				"type" => Type::string(),
			],
			"id" => [
				"name" => "id",
				"type" => Type::int(),
			],
			"limit" => [
				"name" => "limit",
				"type" => Type::int(),
			],
			"page" => [
				"name" => "page",
				"type" => Type::int(),
			],
			"sort" => [
				"name" => "sort",
				"type" => Type::string(),
			],
		];

		return $defaultArgs;
	}
}
