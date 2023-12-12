<?php

namespace Tests\Feature\Mutations\Species;

use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class SpeciesCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected $query = '
      mutation SpeciesCreateMutation($input: speciesCreateInput!) {
        speciesCreate (input: $input) {
          data {
            name
          }
        }
      }';

	public function test_species_cannot_be_created_without_name()
	{
		$variables = [
			"input" => [
				"name" => null,
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$this->assertContains(
			"The name must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_species_can_be_created()
	{
		$variables = [
			"input" => [
				"name" => "Test Species",
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"speciesCreate" => [
						"data" => [
							"*" => ["name"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"speciesCreate" => [
						"data" => [["name" => "Test Species"]],
					],
				],
			]);
	}

	public function test_species_must_be_unique()
	{
		$variables = [
			"input" => [
				"name" => "Test Species",
			],
		];

		// Create a species
		$this->postGraphqlJsonWithVariables($this->query, $variables);

		// Attempt to create another species with same name
		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$this->assertContains(
			"The name must be unique",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
