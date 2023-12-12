<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\StatusNameResolver;

class StatusNameArguments extends NameArguments
{
	public static $resolver = StatusNameResolver::class;
}
