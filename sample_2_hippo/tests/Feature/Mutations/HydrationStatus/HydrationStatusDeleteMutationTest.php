<?php

namespace Tests\Feature\Mutations\HydrationStatus;

use App\Models\HydrationStatus;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class HydrationStatusDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_hydration_status_delete()
	{
		$hydrationStatus = HydrationStatus::factory()->create();

		$query = '
      mutation HSOptionDeleteMutation($id: Int!) {
        hydrationStatusDelete(id: $id) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"id" => "{$hydrationStatus->id}",
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"hydrationStatusDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}

	public function test_hydration_status_can_not_delete_without_id()
	{
		$hydrationStatus = HydrationStatus::factory()->create();

		$query = '
      mutation HSOptionDeleteMutation($id: Int!) {
        hydrationStatusDelete(id: $id) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"id" => null,
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200);
		$this->assertContains(
			'Variable "$id" got invalid value null; Expected non-nullable type Int! not to be null.',
			$response->json("*.*.message"), //"*.*.extensions.validation.*.*"
		);
	}

	public function test_hydration_status_can_not_delete_without_valid_id()
	{
		$hydrationStatus = HydrationStatus::factory()->create([
			"id" => 404,
		]);

		$query = '
      mutation HSOptionDeleteMutation($id: Int!) {
        hydrationStatusDelete(id: $id) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"id" => 401,
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200);
		$this->assertContains(
			"No query results for model [App\\Models\\HydrationStatus] 401",
			$response->json("*.*.debugMessage"),
		);
	}
}
