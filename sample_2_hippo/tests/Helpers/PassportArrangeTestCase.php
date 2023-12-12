<?php

namespace Tests\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;

class PassportArrangeTestCase extends TestCase
{
	/**
	 * Setup Passport and create a user
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();

		$this->artisan("passport:install");

		$this->user = User::factory()
			->connection("hippodb_test")
			->create();

		//you must add roles and permissions or things get real goofy real quick
		DB::connection("hippodb_test")
			->table("tblUserAccessLevels")
			->insertUsing(
				["user_id", "access_level"],
				DB::connection("hippodb_test")
					->table("roles")
					->select(DB::raw(1), "tblAccessLevels.al")
					->join(
						"tblAccessLevels",
						"roles.name",
						"=",
						"tblAccessLevels.access_level",
					)
					->where("roles.id", 1),
			);

		$this->token = Passport::actingAs($this->user)->createToken(
			"Subdomain User Access Token",
			[],
		)->accessToken;
		$this->headers["Accept"] = "application/json";
		$this->headers["Authorization"] = "Bearer " . $this->token;
	}

	/**
	 * Merge the headers with the Authorization
	 *
	 * @return array
	 */
	public function getMergeHeaders(): array
	{
		return array_merge($this->headers, [
			"Content-Type" => "application/json",
			"referer" => "https://test.hippo.test/",
			"origin" => "https://test.hippo.test/",
			"accept" => "application/json, text/plain, */*",
			"subdomain" => "test",
		]);
	}

	/**
	 * Create the cookie array with the crated token
	 *
	 * @return array
	 */
	public function getMergedCookies(): array
	{
		return [
			"_subdomain_token_test" => $this->token,
		];
	}

	/**
	 * Creates a postJson with required passport tokens and authorization
	 *
	 * @param string $uri
	 * @param string $query
	 * @return TestResponse
	 */
	public function postGraphqlJson(string $uri, string $query): TestResponse
	{
		return $this->withCookies($this->getMergedCookies())
			->withHeaders($this->getMergeHeaders())
			->postJson($uri, [
				"query" => $query,
			]);
	}

	/**
	 * @param string $uri
	 * @param string $query
	 * @param array $variables
	 * @return TestResponse
	 */
	public function postGraphqlJsonWithVariables(
		string $uri,
		string $query,
		array $variables
	): TestResponse {
		return $this->withCookies($this->getMergedCookies())
			->withHeaders($this->getMergeHeaders())
			->postJson($uri, [
				"query" => $query,
				"variables" => $variables,
			]);
	}
}
