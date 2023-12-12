<?php

namespace App\Http\Requests\Passport\PersonalAccessToken;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePersonalAccessTokenRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = Auth::user()->load([
			"tokens" => function ($query) {
				$query->where("revoked", "=", false);
			},
		]);

		if (sizeof($user->tokens) + 1 > $user->allowed_api_token_count) {
			return false;
		}

		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"name" => ["required", "string", "max:255"],
		];
	}
}
