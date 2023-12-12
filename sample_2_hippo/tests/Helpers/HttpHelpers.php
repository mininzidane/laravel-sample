<?php

namespace Tests\Helpers;

use Illuminate\Testing\TestResponse;

trait HttpHelpers
{
	/**
	 * Posts a GraphQL query with required passport tokens and authorization.
	 *
	 * @param string $query
	 * @return TestResponse
	 */
	public function postGraphqlJson(string $query): TestResponse
	{
		return $this->withCookies($this->getMergedCookies())
			->withHeaders($this->headers)
			->postJson($this->graphQLEndpoint, [
				"query" => $query,
			]);
	}

	/**
	 * Posts a GraphQL query with variables and required passport tokens and authorization.
	 *
	 * @param string $query
	 * @param array $variables
	 * @return TestResponse
	 */
	public function postGraphqlJsonWithVariables(
		string $query,
		array $variables
	): TestResponse {
		return $this->withCookies($this->getMergedCookies())
			->withHeaders($this->headers)
			->postJson($this->graphQLEndpoint, [
				"query" => $query,
				"variables" => $variables,
			]);
	}

	/**
	 * Posts to a Laravel endpoint query with required passport tokens and authorization.
	 *
	 * @param string $uri
	 * @return TestResponse
	 */
	public function postUriWithCookiesAndHeaders(string $uri): TestResponse
	{
		return $this->withCookies($this->getMergedCookies())
			->withHeaders($this->headers)
			->postJson($uri);
	}

	/**
	 * Posts to a Laravel endpoint query with required passport tokens and authorization.
	 * Could just remove this all together since it's just strait endpoint posting
	 *
	 * @param string $uri
	 * @return TestResponse
	 */
	public function postUriNoCookiesAndHeaders(string $uri): TestResponse
	{
		return $this->postJson($uri);
	}
}
