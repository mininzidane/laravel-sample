<?php

namespace App\Exceptions;

class SubdomainNotConfiguredException extends HippoException
{
	public function __construct($subdomain)
	{
		$message =
			"The subdomain " .
			$subdomain .
			" is not configured and cannot be processed";

		parent::__construct($message, 500);
	}
}
