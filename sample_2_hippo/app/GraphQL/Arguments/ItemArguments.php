<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\ItemResolver;
use GraphQL\Type\Definition\Type;

class ItemArguments extends AdditionalArguments
{
	public static $resolver = ItemResolver::class;

	public function getArguments()
	{
		return [
			"category" => [
				"name" => "category",
				"type" => Type::string(),
			],
			"upcNumber" => [
				"name" => "upcNumber",
				"type" => Type::string(),
			],
			"typeId" => [
				"name" => "typeId",
				"type" => Type::string(),
			],
			"location" => [
				"name" => "location",
				"type" => Type::id(),
			],
			"isVaccine" => [
				"name" => "isVaccine",
				"type" => Type::boolean(),
			],
			"isSalesRegister" => [
				"name" => "isSalesRegister",
				"type" => Type::boolean(),
			],
			"checkoutSearch" => [
				"name" => "checkoutSearch",
				"type" => Type::string(),
			],
			"receivingSearch" => [
				"name" => "receivingSearch",
				"type" => Type::string(),
			],
		];
	}
}
