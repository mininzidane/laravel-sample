<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Tax;

use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class TaxCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = 'mutation TaxCreateMutation($name: String, $percent: Float) {
              taxCreate (input: {
                  id: "",
                  name: $name,
                  percent: $percent
              }) {
                  data {
                      id,
                      percent
                  }
              }
          }
    ';

	public function test_can_create_tax(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"name" => "test tax",
			"percent" => 77,
		]);

		$response->assertJsonStructure([
			"data" => [
				"taxCreate" => [
					"data" => [
						"*" => ["id", "percent"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"taxCreate" => [
					"data" => [
						[
							"id" => "1",
							"percent" => 77,
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("taxes", [
			"id" => 1,
			"percent" => 77,
			"name" => "test tax",
		]);
	}

	public function test_name_is_blank(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"name" => "",
			"percent" => 77,
		]);
		$this->assertContains(
			"The value must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_name_duplicate(): void
	{
		$this->postGraphqlJsonWithVariables($this->query, [
			"name" => "test1",
			"percent" => 77,
		]);
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"name" => "test1",
			"percent" => 77,
		]);
		$this->assertContains(
			"The name must be unique",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_percent_is_blank(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"name" => "test name",
			"percent" => "",
		]);
		$this->assertContains(
			"The value must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_percent_is_incorrect(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"name" => "test name",
			"percent" => 0,
		]);
		$this->assertContains(
			"The value must be greater than zero",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
