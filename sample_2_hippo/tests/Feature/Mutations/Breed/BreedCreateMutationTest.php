<?php

namespace Tests\Feature\Mutations\Breed;

use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class BreedCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected $query = '
        mutation BreedCreateMutation($input: breedCreateInput!) {
            breedCreate(input: $input) {
                data {
                    name
                }
            }
        }';

	protected $species;

	public function setUp(): void
	{
		parent::setUp();
		$this->species = $this->create_species();
	}

	public function test_breed_cannot_be_created_without_name()
	{
		$variables = [
			"input" => [
				"name" => null,
				"species" => $this->species["name"],
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

	public function test_breed_cannot_be_created_without_species_name()
	{
		$variables = [
			"input" => [
				"name" => "Test Breed Unique",
				"species" => null,
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$this->assertContains(
			"A species must be selected",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_breed_cannot_be_created_without_existing_species()
	{
		$variables = [
			"input" => [
				"name" => "Test Breed Unique",
				"species" => "Non-existing Species",
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		// Mutation rule only specifies required not tblSpecies existence
		// $response contains database error not easily parsed
		$this->assertContains(
			"Internal server error",
			$response->json("*.*.*"),
		);
	}

	public function test_breed_can_be_created()
	{
		$variables = [
			"input" => [
				"name" => "Test Breed",
				"species" => $this->species["name"],
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
					"breedCreate" => [
						"data" => [
							"*" => ["name"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"breedCreate" => [
						"data" => [["name" => "Test Breed"]],
					],
				],
			]);
	}

	public function test_breed_must_be_unique_for_a_given_species()
	{
		$variables = [
			"input" => [
				"name" => "Test Breed",
				"species" => $this->species["name"],
			],
		];

		// Create a breed
		$this->postGraphqlJsonWithVariables($this->query, $variables);

		// Attempt to create another breed with same species name
		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$this->assertContains(
			"The name must be unique for a selected species",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
