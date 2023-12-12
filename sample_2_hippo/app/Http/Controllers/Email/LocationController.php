<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Organization;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Postmark\PostmarkClient;

class LocationController extends Controller
{
	public function setEmailVerified(Request $request)
	{
		if ($request->filled("email", "token")) {
			$location = $this->checkTokenForEmail(
				$request,
				"tblLocationVerificationTokens",
			);
			if ($location) {
				$location->email_identity_verified = true;
				$location->save();
				DB::connection($request->header("Subdomain"))
					->table("tblLocationVerificationTokens")
					->where("location_id", $location->id)
					->delete();

				return response($location->name);
			}
			return response($this->error);
		}
	}

	protected function checkTokenForEmail(Request $request, string $tokenTable)
	{
		$subdomain = $request->header("Subdomain");
		$this->createSubdomainConnection($subdomain);
		$location = Location::on($subdomain)
			->where("email", $request->email)
			->first();
		if (!$location) {
			$this->error = "Location email not found";
			return false;
		}
		$token = DB::connection($subdomain)
			->table($tokenTable)
			->where("location_id", $location->id)
			->where("token", $request->token)
			->first();
		if (!$token) {
			$this->error = "Token not found";
			return false;
		}
		return $location;
	}

	protected function generateLocationVerificationUrl(
		string $email,
		Request $request,
		string $verificationToken
	): string {
		$urlParameters = http_build_query([
			"e" => $email,
			// "v" parameter indicates to the app this is a Verification request, as opposed
			// to a password reset or forgot password request
			"v" => 1,
			"vt" => $verificationToken,
		]);
		return $request->headers->get("referer") .
			"location-verify?${urlParameters}";
	}

	public function sendLocationVerification(Request $request, int $locationId)
	{
		$subdomain = $request->header("Subdomain");
		$this->createSubdomainConnection($subdomain);
		$organization = Organization::on($subdomain)->first();
		$postmark = new PostmarkClient(Config::get("services.postmark.token"));
		$location = Location::on($subdomain)->findOrFail($locationId);
		$verificationLink = $this->generateLocationVerificationUrl(
			$location->email,
			$request,
			$this->generateAndSaveToken(
				$locationId,
				$request,
				"tblLocationVerificationTokens",
			),
		);
		$data = [
			"organization" => [
				"name" => $organization->name,
				"logo" => $organization->imageUrl,
			],
			"verification_link" => $verificationLink,
		];
		$postmark->sendEmailWithTemplate(
			"Hippo Manager <info@hippomanager.com>",
			$location->email,
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

	protected function generateAndSaveToken(
		int $locationId,
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
				"location_id" => $locationId,
				"token" => $token,
			]);
		return $token;
	}
}
