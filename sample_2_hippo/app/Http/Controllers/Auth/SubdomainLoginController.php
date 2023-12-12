<?php

namespace App\Http\Controllers\Auth;

use App\Extensions\Providers\SubdomainEloquentUserProvider;
use App\Http\Controllers\Controller;
use App\Models\Passport\Token;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\TokenRepository;

class SubdomainLoginController extends Controller
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

	/**
	 * The token repository implementation.
	 *
	 * @var TokenRepository
	 */
	protected $tokenRepository;

	/**
	 * The validation factory implementation.
	 *
	 * @var ValidationFactory
	 */
	protected $validation;

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
	 * @param TokenRepository $tokenRepository
	 * @param ValidationFactory $validation
	 * @return void
	 */
	public function __construct(
		TokenRepository $tokenRepository,
		ValidationFactory $validation
	) {
		$this->validation = $validation;
		$this->tokenRepository = $tokenRepository;
	}

	/**
	 * Attempt to log the user into the application.
	 *
	 * @param Request $request
	 * @return bool
	 */
	protected function attemptLogin(Request $request)
	{
		return $this->guard($request)->attempt(
			$this->credentials($request),
			$request->filled("remember"),
		);
	}

	/**
	 * Get the guard to be used during authentication.
	 *
	 * @param Request $request
	 * @return StatefulGuard
	 */
	protected function guard(Request $request = null)
	{
		if ($request) {
			Auth::setProvider(
				new SubdomainEloquentUserProvider(
					$request->header("Subdomain"),
				),
			);

			return Auth::guard();
		}

		return Auth::guard("api-subdomain-passport");
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username()
	{
		return "username";
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
		$request->validate([
			$this->username() => "required|string",
			"password" => "required|string",
			"subdomain" => "required|string",
		]);
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param Request $request
	 * @param mixed $user
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user)
	{
		// If the user already has a token, revoke it before issuing a new one
		if (
			Auth::guard("api-subdomain-passport")->user() &&
			Auth::guard("api-subdomain-passport")
				->user()
				->token()
		) {
			Auth::guard("api-subdomain-passport")
				->user()
				->token()
				->delete();
			Token::where("user_id", $this->guard()->user()->id)->delete();
		}

		$token = $user->createToken("Subdomain User Access Token")->accessToken;

		$tokenCookie = $this->getCookieDetails(
			"_subdomain_token_" . $request->header("subdomain"),
			$token,
		);

		return response()
			->json(
				[
					"user" => $user,
				],
				200,
			)
			->cookie(
				$tokenCookie["name"],
				$tokenCookie["value"],
				$tokenCookie["minutes"],
				$tokenCookie["path"],
				$tokenCookie["domain"],
				$tokenCookie["secure"],
				$tokenCookie["httponly"],
				$tokenCookie["samesite"],
			);
	}

	/**
	 * Send the response after the user was authenticated.
	 *
	 * @param Request $request
	 * @return Response
	 */
	protected function sendLoginResponse(Request $request)
	{
		$this->clearLoginAttempts($request);

		if (
			$response = $this->authenticated(
				$request,
				$this->guard($request)->user(),
			)
		) {
			return $response;
		}

		return $request->wantsJson()
			? new Response("", 204)
			: redirect()->intended($this->redirectPath());
	}

	private function getCookieDetails($name, $value)
	{
		$domain = config("app.env") === "local" ? "hippo.test" : "hippo.vet";

		return [
			"name" => $name,
			"value" => $value,
			"minutes" => 1440,
			"path" => null,
			"domain" => $domain,
			//secure = true,
			"secure" => false,
			"httponly" => true,
			"samesite" => true,
		];
	}

	public function me(Request $request)
	{
		return response()->json($this->guard()->user());
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function logout(Request $request)
	{
		if (!$this->guard()->check()) {
			return new Response("", 200);
		}

		$userId = $this->guard()->user()->id;

		$this->guard()
			->user()
			->token()
			->revoke();

		$this->guard($request)->logout();

		$domain = config("app.env") === "local" ? "hippo.test" : "hippo.vet";
		Cookie::queue(
			Cookie::forget(
				"_subdomain_token_" . $request->header("Subdomain"),
				null,
				$domain,
			),
		);
	}

	/**
	 * Delete the given token.
	 *
	 * @param Request $request
	 * @param string $tokenId
	 * @return Response
	 */
	public function destroy(Request $request, $tokenId)
	{
		$token = $this->tokenRepository->findForUser(
			$tokenId,
			$request->user()->getAuthIdentifier(),
		);

		if (is_null($token)) {
			return new Response("", 404);
		}

		$token->revoke();

		return new Response("", Response::HTTP_NO_CONTENT);
	}
}
