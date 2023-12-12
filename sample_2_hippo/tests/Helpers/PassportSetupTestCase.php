<?php

namespace Tests\Helpers;

use App\Models\User;
use App\Models\UserAccessLevel;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PassportSetupTestCase extends TestCase
{
	use HttpHelpers;

	protected const DATETIME_FORMAT = "Y-m-d H:i:s";

	/**
	 * @var array
	 */
	public $headers;

	private $user;
	/**
	 * @var string
	 */
	private $token;

	public $graphQLEndpoint;

	/**
	 * @var Carbon
	 */
	protected $carbonTestTime;

	public function setUp(): void
	{
		parent::setUp();

		//TODO: create testing custom testing configurations
		//TODO: allow ability to pass testing configurations in
		$subdomain = config("testing.subdomain");
		$connection = config("testing.default");
		$database = config("testing.connections.$connection.database");
		$this->graphQLEndpoint = config("testing.graphQLEndpoint");

		$role = $this->getRole(); // Call a method to get the role for the test case

		$this->clearPermissionsCache();

		$this->installPassport();

		$this->createUserWithAccessLevel($database, $role);

		$this->createAccessToken();

		$this->setUpHeaders($subdomain);

		$this->setCarbonTestTime();
	}

	protected function clearPermissionsCache()
	{
		$this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
	}

	protected function installPassport()
	{
		$this->artisan("passport:install");
	}

	protected function createUserWithAccessLevel($database, $role)
	{
		$this->user = User::factory()
			->connection($database)
			->create();

		$userAccessLevel = UserAccessLevel::factory()->create([
			"user_id" => $this->user->id,
			"access_level" => $role,
		]);
	}

	protected function createAccessToken()
	{
		$this->token = Passport::actingAs($this->user)->createToken(
			"Subdomain User Access Token",
			[],
		)->accessToken;
	}

	protected function setUpHeaders($subdomain)
	{
		$this->headers = [
			"Accept" => "application/json",
			"Authorization" => "Bearer $this->token",
			"referer" => config("testing.url"),
			"origin" => config("testing.url"),
			"accept" => "application/json, text/plain, */*",
			"subdomain" => $subdomain,
		];
	}

	/**
	 * Create the cookie array with the created token.
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
	 * Get the role for the current test case.
	 *
	 * @return string
	 */
	protected function getRole(): string
	{
		return $this->role ?? "super_user";
	}

	public function setCarbonTestTime()
	{
		$this->carbonTestTime = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($this->carbonTestTime);
	}
}
