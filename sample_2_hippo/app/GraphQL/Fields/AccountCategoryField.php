<?php

namespace App\GraphQL\Fields;

use App\GraphQL\Types\AccountCategoryGraphQLType;

class AccountCategoryField extends HippoField
{
	protected $graphQLType = AccountCategoryGraphQLType::class;
	protected $permissionName = "GraphQL: View Account Categories";
	protected $isList = false;

	protected $attributes = [
		"description" => "Associated Account Categories",
	];
}
