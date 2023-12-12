<?php

namespace Tests\Feature\Mutations\Species;

use App\Models\Species;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class SpeciesUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected $query = '
      mutation SpeciesUpdateMutation($id: Int!, $input: speciesUpdateInput!) {
        speciesUpdate (id: $id, input: $input) {
          data {
            id
          }
        }
      }
    ';

	/*
	 * This test will fail until API verification is fixed for empty name
	 * See Jira ticket HMD-1423 (https://hippomanager.atlassian.net/browse/HMD-1423)
	 */
	public function test_species_cannot_be_modified_without_name()
	{
		$species = Species::factory()->create();

		$variables = [
			"id" => $species->id,
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

	public function test_species_must_be_unique()
	{
		$species1 = Species::factory()->create();
		$species2 = Species::factory()->create();

		// attempt to edit second species' name to the existing one
		$variables = [
			"id" => $species2->id,
			"input" => [
				"name" => $species1->name,
			],
		];

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
