<?php

namespace App\Http\Requests\LabRequisition;

use Illuminate\Foundation\Http\FormRequest;

class LabRequisitionRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
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
			"status" => ["required", "string", "max:255"],
		];
	}
}
