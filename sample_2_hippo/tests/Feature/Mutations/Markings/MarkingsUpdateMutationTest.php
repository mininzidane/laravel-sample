<?php

namespace Tests\Feature\Mutations\Markings;

use App\Models\Markings;
use App\Models\Species;
use Faker\Factory;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class MarkingsUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	private $markingsToUpdate;
	private $newMarkingsName;
	const UPDATE_QUERY = '
            mutation MarkingsUpdateMutation($id: Int!, $input: markingsUpdateInput) {
                markingsUpdate (id: $id, input: $input) {
                    data {
                        id
                        species
                        name
                    }
                }
            }
        ';

	public function setUp(): void
	{
		parent::setUp();
		$this->markingsToUpdate = Markings::factory()->create();
		$this->newMarkingsName = Factory::create()->name;
	}

	/**
	 * Test if Markings can be updated successfully with expected inputs.
	 * @return void
	 */
	public function test_markings_can_be_updated()
	{
		$newSpeciesName = Species::factory()->create()->name;

		$this->postGraphqlJsonWithVariables(self::UPDATE_QUERY, [
			"id" => $this->markingsToUpdate->id,
			"input" => [
				"species" => $newSpeciesName,
				"name" => $this->newMarkingsName,
			],
		])
			->assertStatus(200)
			->assertExactJson([
				"data" => [
					"markingsUpdate" => [
						"data" => [
							[
								// For whatever reason the ID is returned as a string in JSON.
								"id" => strval($this->markingsToUpdate->id),
								"species" => $newSpeciesName,
								"name" => $this->newMarkingsName,
							],
						],
					],
				],
			]);
	}

	/**
	 * Test if Markings fail to be updated when using a name that is not unique for that species.
	 * @return void
	 */
	public function test_markings_cannot_be_updated_without_unique_name()
	{
		$input = [
			"id" => $this->markingsToUpdate->id,
			"input" => [
				"species" => Species::factory()->create()->name,
				"name" => $this->newMarkingsName,
			],
		];

		// Update our initial Marking with the generated name.
		$this->postGraphqlJsonWithVariables(
			self::UPDATE_QUERY,
			$input,
		)->assertStatus(200);

		// Create a second marking with the same data from the initial input, but change the Marking ID to a new one.
		$input["id"] = Markings::factory()->create()->id;

		$response = $this->postGraphqlJsonWithVariables(
			self::UPDATE_QUERY,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"The name must be unique for the selected species",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	/**
	 * Test if Markings fail to be updated when no species key is present.
	 * @return void
	 */
	public function test_markings_cannot_be_updated_without_species_key()
	{
		$response = $this->postGraphqlJsonWithVariables(self::UPDATE_QUERY, [
			"id" => $this->markingsToUpdate->id,
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
	 * Test if Markings fail to be updated when no name key is present.
	 * @return void
	 */
	public function test_markings_cannot_be_updated_without_name_key()
	{
		$response = $this->postGraphqlJsonWithVariables(self::UPDATE_QUERY, [
			"id" => $this->markingsToUpdate->id,
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
	 * Test if Markings fail to be updated when the species value is null.
	 * @return void
	 */
	public function test_markings_cannot_be_updated_with_null_species()
	{
		$response = $this->postGraphqlJsonWithVariables(self::UPDATE_QUERY, [
			"id" => $this->markingsToUpdate->id,
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
	 * Test if Markings fail to be updated when the name value is null.
	 * @return void
	 */
	public function test_markings_cannot_be_updated_with_null_name()
	{
		$response = $this->postGraphqlJsonWithVariables(self::UPDATE_QUERY, [
			"id" => $this->markingsToUpdate->id,
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
