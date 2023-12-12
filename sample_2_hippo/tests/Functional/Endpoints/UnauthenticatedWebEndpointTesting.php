<?php

namespace Tests\Functional\Endpoints;

use Tests\Helpers\TruncateDatabase;
use Tests\TestCase;

class UnauthenticatedWebEndpointTesting extends TestCase
{
	//By using Test case we bypass any auth.  This would like a user hitting our endpoints from the outside world
	use TruncateDatabase;

	public function getHeaders(): array
	{
		return [
			"subdomain" => "test",
		];
	}

	public function getSubdomain(): string
	{
		return $this->getSubdomain();
	}

	/**
	 * Test unauthenticated request to /status endpoint
	 */
	public function testStatusEndpointUnauthenticatedRequest()
	{
		$response = $this->get("/status");
		$response->assertStatus(200);
	}

	/**
	 * Test unauthenticated request to /oauth/scopes endpoint
	 */
	public function testOauthScopesEndpointUnauthenticatedRequest()
	{
		$response = $this->get("/oauth/scopes");
		$response->assertRedirect("/welcome");
	}

	/**
	 * Test unauthenticated request to /oauth/personal-access-tokens endpoint
	 */
	public function testOauthPersonalAccessTokensEndpointUnauthenticatedRequest()
	{
		$response = $this->get("/oauth/personal-access-tokens");
		$response->assertRedirect("/welcome");
	}

	/**
	 * Test unauthenticated request to /oauth/personal-access-tokens/{token_id} endpoint
	 */
	public function testOauthPersonalAccessTokensWithIdEndpointUnauthenticatedRequest()
	{
		$response = $this->delete("/oauth/personal-access-tokens/1");
		$response->assertRedirect("/welcome");
	}

	/**
	 * Test unauthenticated request to / endpoint
	 */
	public function testHomeEndpointUnauthenticatedRequest()
	{
		$response = $this->get("/");
		$response->assertRedirect("/welcome");
	}

	/**
	 * Test unauthenticated request to /cache-clear endpoint
	 */
	public function testCacheClearEndpointUnauthenticatedRequest()
	{
		$response = $this->get("/cache-clear");
		$response->assertStatus(403);
	}
}
