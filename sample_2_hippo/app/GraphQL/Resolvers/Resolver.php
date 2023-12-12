<?php

namespace App\GraphQL\Resolvers;

use Closure;

interface Resolver
{
	public function getQuery(Closure $getSelectFields);

	public function getArgs();
}
