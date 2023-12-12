<?php

namespace Tests\Feature\Mutations\Color;

use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class ColorCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected $query = '
        mutation ColorCreateMutation($input: colorCreateInput!) {
            colorCreate(input: $input) {
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

	public function test_color_cannot_be_created_without_name()
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
			"The color must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_color_cannot_be_created_without_species_name()
	{
		$variables = [
			"input" => [
				"name" => "Test Color Unique",
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

	public function test_color_cannot_be_created_without_existing_species()
	{
		$variables = [
			"input" => [
				"name" => "Test Color Unique",
				"species" => "Non-existing Species",
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		// Mutation rule only specifies required, not tblSpecies existence
		// $response contains database error not easily parsed
		$this->assertContains(
			"Internal server error",
			$response->json("*.*.*"),
		);
	}

	public function test_Color_can_be_created()
	{
		$variables = [
			"input" => [
				"name" => "Test Color",
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
					"colorCreate" => [
						"data" => [
							"*" => ["name"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"colorCreate" => [
						"data" => [["name" => "Test Color"]],
					],
				],
			]);
	}

	public function test_Color_must_be_unique_for_a_given_species()
	{
		$variables = [
			"input" => [
				"name" => "Test Color",
				"species" => $this->species["name"],
			],
		];

		// Create a Color
		$this->postGraphqlJsonWithVariables($this->query, $variables);

		// Attempt to create another Color with same species name
		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$this->assertContains(
			"The color must be unique for a selected species",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
