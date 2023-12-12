<?php

namespace Tests\Feature\Mutations\HydrationStatus;

use App\Models\HydrationStatus;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class HydrationStatusCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_hydration_status_creation()
	{
		$hydrationStatus = ["label" => "HYDRTN STAT", "abbr" => "HS"];

		$query = '
      mutation HSOptionCreateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"input" => [
				"abbreviation" => $hydrationStatus["abbr"],
				"name" => $hydrationStatus["label"],
			],
		];
		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"hydrationStatusCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}

	public function test_can_not_be_created_without_label()
	{
		$hydrationStatus = ["label" => null, "abbr" => "HS"];

		$query = '
      mutation HSOptionCreateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"input" => [
				"abbreviation" => $hydrationStatus["abbr"],
				"name" => $hydrationStatus["label"],
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

	public function test_can_not_be_created_without_unique_label()
	{
		$hydrationStatus = HydrationStatus::factory()->create([
			"label" => "the label",
		]);
		$newHydrationStatus = ["label" => "the label", "abbr" => "HS"];

		$query = '
      mutation HSOptionCreateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"input" => [
				"abbreviation" => $newHydrationStatus["abbr"],
				"name" => $newHydrationStatus["label"],
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

	public function test_can_not_be_created_without_unique_abbreviation()
	{
		$hydrationStatus = HydrationStatus::factory()->create([
			"abbr" => "the abbr",
		]);
		$newHydrationStatus = ["label" => "the label", "abbr" => "the abbr"];

		$query = '
      mutation HSOptionCreateMutation($input: hydrationStatusCreateInput!) {
        hydrationStatusCreate (input: $input) {
          data {
            id
          }
        }
      }
    ';
		$input = [
			"input" => [
				"abbreviation" => $newHydrationStatus["abbr"],
				"name" => $newHydrationStatus["label"],
			],
		];
		$response = $this->postGraphqlJsonWithVariables(
			$query,
			$input,
		)->assertStatus(200);
		$this->assertContains(
			"A abbreviation must be unique", // grammar much?
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
