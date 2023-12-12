<?php

namespace Tests\Feature\Mutations\Color;

use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class ColorUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected $query = '
        mutation ColorUpdateMutation($id: Int!, $input: colorUpdateInput!) {
            colorUpdate(id: $id, input: $input) {
                data {
                    id
                }
            }
        }
    ';

	public function test_color_can_not_be_modified_without_name()
	{
		$color = $this->create_color();

		$variables = [
			"id" => $color["id"],
			"input" => [
				"name" => null,
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

	public function test_color_can_not_be_modified_without_species_name()
	{
		$color = $this->create_color();

		$variables = [
			"id" => $color["id"],
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
