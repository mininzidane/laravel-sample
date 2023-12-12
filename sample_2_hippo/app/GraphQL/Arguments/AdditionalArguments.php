<?php

namespace App\GraphQL\Arguments;

use App\GraphQL\Resolvers\HippoResolver;

abstract class AdditionalArguments implements Arguments
{
	public static $resolver = HippoResolver::class;
	protected $argService = null;

	public function __construct(Arguments $argService)
	{
		$this->argService = $argService;
	}

	public static function getResolver()
	{
		return static::$resolver;
	}

	public function getArgs()
	{
		$args = $this->getArguments();

		return array_merge($this->argService->getArgs(), $args);
	}

	public function getArguments()
	{
		return [];
	}
}
