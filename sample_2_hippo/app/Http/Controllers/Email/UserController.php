<?php

namespace App\Http\Controllers\Email;

use App\Extensions\Hashing\SHA1Hasher;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Config;
use Postmark\PostmarkClient;

class UserController extends Controller
{
	protected $error = "";

	public function sendPasswordReset(Request $request, string $email)
	{
		$subdomain = $request->header("Subdomain");

		$this->createSubdomainConnection($subdomain);

		$postmark = new PostmarkClient(Config::get("services.postmark.token"));

		$organization = Organization::on($subdomain)->first();

		$user = User::on($subdomain)
			->where("username", $email)
			->first();

		if (!$user) {
			return response("Email not found");
		}

		$resetLink = $this->generateResetUrl(
			$user,
			$request,
			$this->generateAndSaveToken(
				$user->id,
				$request,
				"tblUserResetTokens",
			),
		);

		$data = [
			"organization" => [
				"name" => $organization->name,
				"logo" => $organization->imageUrl,
			],
			"reset_link" => $resetLink,
		];

		$emailResult = $postmark->sendEmailWithTemplate(
			"Hippo Manager <info@hippomanager.com>",
			$user->username,
			"hm-password-reset",
			$data,
			true,
			"Password Reset",
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			[
				"Subdomain" => $subdomain,
				"OrgID" => $organization->id,
			],
		);

		return response("Success");
	}

	public function resetPassword(Request $request)
	{
		if ($request->filled(["email", "token", "password"])) {
			$user = $this->checkTokenForEmail($request, "tblUserResetTokens");
			if ($user) {
				// check user active
				if (!$user->active) {
					return response("User not active");
				}

				// update password
				$hasher = new SHA1Hasher();
				$user->password = $hasher->make($request->input("password"), [
					"salt" => $user->getAuthSalt(),
				]);
				$user->save();

				DB::connection($request->header("Subdomain"))
					->table("tblUserResetTokens")
					->where("user_id", $user->id)
					->delete();
			}
		}
	}

	public function setEmailVerified(Request $request)
	{
		if ($request->filled("email", "token")) {
			$user = $this->checkTokenForEmail(
				$request,
				"tblUserVerificationTokens",
			);
			if ($user) {
				$user->email_verified = true;
				$user->email_verified_timestamp = Carbon::now();
				$user->save();
				DB::connection($request->header("Subdomain"))
					->table("tblUserVerificationTokens")
					->where("user_id", $user->id)
					->delete();

				return response($user->username);
			}
			return response($this->error);
		}
	}

	private function checkTokenForEmail(Request $request, string $tokenTable)
	{
		$subdomain = $request->header("Subdomain");
		$this->createSubdomainConnection($subdomain);
		$user = User::on($subdomain)
			->where("username", $request->email)
			->first();
		if (!$user) {
			$this->error = "Email not found";
			return false;
		}
		$token = DB::connection($subdomain)
			->table($tokenTable)
			->where("user_id", $user->id)
			->where("token", $request->token)
			->first();
		if (!$token) {
			$this->error = "Token not found";
			return false;
		}
		return $user;
	}

	private function generateAndSaveToken(
		int $userId,
		Request $request,
		string $tokenTable
	): string {
		$subdomain = $request->header("Subdomain");

		$token = password_hash(
			base64_encode(openssl_random_pseudo_bytes(60)),
			PASSWORD_BCRYPT,
		);
		DB::connection($subdomain)
			->table($tokenTable)
			->insert([
				"user_id" => $userId,
				"token" => $token,
			]);
		return $token;
	}

	private function generateResetUrl(
		User $user,
		Request $request,
		string $token
	): string {
		$urlParameters = http_build_query([
			"e" => $user->username,
			"rt" => $token,
		]);
		return $request->headers->get("referer") . "reset?${urlParameters}";
	}

	private function generateVerificationUrl(
		User $user,
		Request $request,
		string $verificationToken,
		string $resetToken
	): string {
		$urlParameters = http_build_query([
			"e" => $user->username,
			"v" => 1,
			"vt" => $verificationToken,
			"rt" => $resetToken,
		]);
		return $request->headers->get("referer") . "reset?${urlParameters}";
	}

	public function sendUserVerification(Request $request, int $userId)
	{
		$subdomain = $request->header("Subdomain");
		$this->createSubdomainConnection($subdomain);

		$postmark = new PostmarkClient(Config::get("services.postmark.token"));

		$organization = Organization::on($subdomain)->first();

		$user = User::on($subdomain)->findOrFail($userId);

		$verificationLink = $this->generateVerificationUrl(
			$user,
			$request,
			$this->generateAndSaveToken(
				$user->id,
				$request,
				"tblUserVerificationTokens",
			),
			$this->generateAndSaveToken(
				$user->id,
				$request,
				"tblUserResetTokens",
			),
		);

		$data = [
			"organization" => [
				"name" => $organization->name,
				"logo" => $organization->imageUrl,
			],
			"verification_link" => $verificationLink,
		];

		$emailResult = $postmark->sendEmailWithTemplate(
			"Hippo Manager <info@hippomanager.com>",
			$user->username,
			"hm-email-verification",
			$data,
			true,
			"Email Verification",
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			[
				"Subdomain" => $subdomain,
				"OrgID" => $organization->id,
			],
		);

		return response("Success");
	}
}
