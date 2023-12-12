<?php

namespace App\GraphQL\Resolvers;

use App\Models\HippoModel;
use Closure;

abstract class HippoResolver implements Resolver
{
	protected $resolver;
	protected $model;

	public function __construct(Resolver $resolver, HippoModel $model)
	{
		$this->resolver = $resolver;
		$this->model = $model;
	}

	public function getQuery(Closure $getSelectFields)
	{
	}

	public function getArgs()
	{
		return $this->resolver->getArgs();
	}
}
