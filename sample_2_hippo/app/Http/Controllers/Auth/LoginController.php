<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = RouteServiceProvider::HOME;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware("guest")->except("logout");
	}

	/**
	 * Validate the user login request.
	 *
	 * @param Request $request
	 * @return void
	 *
	 * @throws ValidationException
	 */
	protected function validateLogin(Request $request)
	{
		$rules = [
			$this->username() => "required|string",
			"password" => "required|string",
		];

		if (!App::isLocal()) {
			$rules["g-recaptcha-response"] = "required|captcha";
		}

		$request->validate($rules);
	}
}
