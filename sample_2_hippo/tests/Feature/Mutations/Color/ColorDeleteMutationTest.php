<?php

namespace Tests\Feature\Mutations\Color;

use App\Models\Patient;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class ColorDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected $query = '
        mutation ColorDeleteMutation($id: Int!) {
            colorDelete(id: $id) {
                data {
                    id
                }
            }
        }
    ';

	public function test_color_can_be_deleted()
	{
		$color = $this->create_color();

		$variables = [
			"id" => intval($color["id"]),
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"colorDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}

	// Uncomment this test after tblClients is set up with
	// a foreign key constraint to tblColors (see HM-688 in Jira)
	/*
    public function test_color_cannot_be_deleted_when_referenced() {
        $color = $this->create_color();
        Patient::factory()->create(["color" => $color["name"]]);

        $variables = [
            "id" => intval($color["id"])
        ];

        $response = $this->postGraphqlJsonWithVariables(
            "https://api.hippo.test/graphql/app",
            $this->query,
            $variables,
        );

        $this->assertContains(
            "Internal server error",
            $response->json("*.*.*")
        );
    }
    */
}
