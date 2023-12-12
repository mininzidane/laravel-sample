<?php

namespace Tests\Feature\Mutations\Markings;

use App\Models\Species;
use Faker\Factory;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class MarkingsCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	private $newMarkingsName;
	const CREATE_QUERY = '
            mutation MarkingsCreateMutation($input: markingsCreateInput!) {
                markingsCreate (input: $input) {
                    data {
                        id
                    }
                }
            }
        ';

	public function setUp(): void
	{
		parent::setUp();
		$this->newMarkingsName = Factory::create()->name;
	}

	/**
	 * Test if Markings can be created successfully with expected inputs.
	 * @return void
	 */
	public function test_markings_can_be_created()
	{
		$this->postGraphqlJsonWithVariables(self::CREATE_QUERY, [
			"input" => [
				"species" => Species::factory()->create()->name,
				"name" => $this->newMarkingsName,
			],
		])
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"markingsCreate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			]);
	}

	/**
	 * Test if Markings fail to be created when using a name that is not unique for that species.
	 * @return void
	 */
	public function test_markings_cannot_be_created_without_unique_name()
	{
		$input = [
			"input" => [
				"species" => Species::factory()->create()->name,
				"name" => $this->newMarkingsName,
			],
		];

		// The first query should succeed, the second should fail.
		$this->postGraphqlJsonWithVariables(
			self::CREATE_QUERY,
			$input,
		)->assertStatus(200);
		$response = $this->postGraphqlJsonWithVariables(
			self::CREATE_QUERY,
			$input,
		)->assertStatus(200);

		$this->assertContains(
			"The name must be unique for a selected species",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	/**
	 * Test if Markings fail to be created when no species key is present.
	 * @return void
	 */
	public function test_markings_cannot_be_created_without_species_key()
	{
		$response = $this->postGraphqlJsonWithVariables(self::CREATE_QUERY, [
			"input" => [
				"name" => $this->newMarkingsName,
			],
		])->assertStatus(200);

		$this->assertContains(
			"Undefined index: species",
			$response->json("errors.*.debugMessage"),
		);
	}

	/**
	 * Test if Markings fail to be created when no name key is present.
	 * @return void
	 */
	public function test_markings_cannot_be_created_without_name_key()
	{
		$response = $this->postGraphqlJsonWithVariables(self::CREATE_QUERY, [
			"input" => [
				"species" => Species::factory()->create()->name,
			],
		])->assertStatus(200);

		$this->assertContains(
			"The name must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	/**
	 * Test if Markings fail to be created when the species value is null.
	 * @return void
	 */
	public function test_markings_cannot_be_created_with_null_species()
	{
		$response = $this->postGraphqlJsonWithVariables(self::CREATE_QUERY, [
			"input" => [
				"species" => null,
				"name" => $this->newMarkingsName,
			],
		])->assertStatus(200);

		$this->assertContains(
			"A species must be selected",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	/**
	 * Test if Markings fail to be created when the name value is null.
	 * @return void
	 */
	public function test_markings_cannot_be_created_with_null_name()
	{
		$response = $this->postGraphqlJsonWithVariables(self::CREATE_QUERY, [
			"input" => [
				"species" => Species::factory()->create()->name,
				"name" => null,
			],
		])->assertStatus(200);

		$this->assertContains(
			"The name must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
