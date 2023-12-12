<?php

namespace App\Exceptions;

class SubdomainNotProvidedException extends HippoException
{
	public function __construct()
	{
		$message =
			"No subdomain was available for processing the request.  Please provide a subdomain.";

		parent::__construct($message, 500);
	}
}
