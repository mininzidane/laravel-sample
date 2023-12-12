<?php

namespace Tests\Feature\Mutations\Gender;

use App\Models\Gender;
use App\Models\Species;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class GenderMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_can_create_gender()
	{
		$species = Species::factory()->create();

		$query = '
              mutation GenderCreateMutation($input: genderCreateInput!) {
                genderCreate (input: $input) {
                  data {
                    id
                  }
                }
              }
            ';

		$variables = [
			"input" => [
				"name" => "Test Gender",
				"sex" => "M",
				"neutered" => false,
				"species" => "$species->name",
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"genderCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"genderCreate" => [
					"data" => [
						[
							"id" => "2",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblGenders", [
			"id" => 2,
			"gender" => "Test Gender",
			"species" => "$species->name",
		]);
	}

	public function test_can_update_gender()
	{
		$gender = Gender::factory()->create();

		$query = '
                  mutation GenderUpdateMutation($id: Int!, $input: genderUpdateInput!) {
                    genderUpdate(id: $id, input: $input) {
                      data {
                        id
                      }
                    }
                  }
                ';

		$variables = [
			"id" => $gender->id,
			"input" => [
				"name" => "Test Gender1",
				"sex" => "M",
				"neutered" => true,
				"species" => "$gender->species",
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"genderUpdate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"genderUpdate" => [
					"data" => [
						[
							"id" => "$gender->id",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblGenders", [
			"id" => $gender->id,
			"gender" => "Test Gender1",
			"sex" => "M",
			"neutered" => "1",
			"species" => "$gender->species",
		]);
	}

	public function test_can_delete_gender()
	{
		$gender = Gender::factory()->create();

		$query = '
          mutation GenderDeleteMutation($id: Int!) {
            genderDelete(id: $id) {
              data {
                id
              }
            }
          }
          ';

		$variables = [
			"id" => "$gender->id",
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"genderDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$this->assertDatabaseMissing("tblGenders", [
			"id" => $gender->id,
			"deleted_at" => null,
		]);
	}
}
