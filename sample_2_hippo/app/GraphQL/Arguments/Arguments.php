<?php

namespace App\GraphQL\Arguments;

interface Arguments
{
	public static function getResolver();

	public function getArgs();
}
