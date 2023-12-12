<?php

namespace Tests\Feature\Mutations\HydrationStatus;

use App\Models\HydrationStatus;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class HydrationStatusUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_hydration_status_update()
	{
		$hydrationStatus = HydrationStatus::factory()->create();
		$updatedHydrationStatus = [
			"name" => "New name",
			"abbreviation" => "GG",
		];
		$query = '
			mutation HSOptionUpdateMutation($id: Int!, $input: hydrationStatusUpdateInput!) {
				hydrationStatusUpdate (id: $id, input: $input) {
					data {
						id          
			}}}';
		$input = [
			"id" => $hydrationStatus->id,
			"input" => [
				"abbreviation" => $updatedHydrationStatus["abbreviation"],
				"name" => $updatedHydrationStatus["name"],
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"hydrationStatusUpdate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"hydrationStatusUpdate" => [
						"data" => [
							[
								"id" => "{$hydrationStatus->id}",
							],
						],
					],
				],
			]);

		$query = '
    query HSOptionQuery($id: Int!) {
      hydrationStatuses(id: $id) {
        data {
          id
          name
          abbreviation
        }
      }
    }
   ';
		$input = ["id" => $hydrationStatus->id];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200)->assertExactJson([
			"data" => [
				"hydrationStatuses" => [
					"data" => [
						[
							"id" => "{$hydrationStatus->id}",
							"name" => $updatedHydrationStatus["name"],
							"abbreviation" =>
								$updatedHydrationStatus["abbreviation"],
						],
					],
				],
			],
		]);
	}

	public function test_can_not_be_updated_without_label()
	{
		$hydrationStatus = HydrationStatus::factory()->create();

		$query = '
      mutation HSOptionUpdateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"id" => $hydrationStatus->id,
			"input" => [
				"name" => "",
			],
		];
		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"The label must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_can_not_be_updated_without_unique_label()
	{
		$hydrationStatus1 = HydrationStatus::factory()->create([
			"label" => "the label",
		]);
		$hydrationStatus2 = HydrationStatus::factory()->create();

		$query = '
      mutation HSOptionUpdateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"id" => $hydrationStatus2->id,
			"input" => [
				"name" => "the label",
			],
		];
		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"The label must be unique",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_can_not_be_updated_without_unique_abbreviation()
	{
		$hydrationStatus = HydrationStatus::factory()->create([
			"abbr" => "the abbr",
		]);

		$query = '
      mutation HSOptionUpdateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"id" => $hydrationStatus->id,
			"input" => [
				"abbreviation" => "the abbr",
			],
		];
		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"A abbreviation must be unique",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
