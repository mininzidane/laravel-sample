<?php

namespace App\Http\Requests\Subdomain;

class UpdateSubdomain extends SubdomainRequest
{
	public function rules()
	{
		return [
			"name" => ["sometimes", "string", "max:255"],
			"active" => ["sometimes", "boolean"],
		];
	}
}
