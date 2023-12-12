<?php

namespace Tests\Feature\Mutations\Breed;

use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class BreedUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected $query = '
        mutation BreedUpdateMutation($id: Int!, $input: breedUpdateInput!) {
            breedUpdate(id: $id, input: $input) {
                data {
                    id
                }
            }
        }
    ';

	public function test_breed_can_not_be_modified_without_name()
	{
		$breed = $this->create_breed();

		$variables = [
			"id" => $breed["id"],
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

	public function test_breed_can_not_be_modified_without_species_name()
	{
		$breed = $this->create_breed();

		$variables = [
			"id" => $breed["id"],
			"input" => [
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
}
