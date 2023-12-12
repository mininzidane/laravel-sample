<?php

namespace App\Http\Requests\User;

class UpdateUser extends UserRequest
{
	public function rules()
	{
		return [
			"name" => ["sometimes", "string", "max:255"],
			"email" => [
				"sometimes",
				"string",
				"email",
				"max:255",
				"unique:users,id," . $this->get("id"),
			],
			"password" => ["sometimes", "string", "min:8", "confirmed"],
		];
	}
}
