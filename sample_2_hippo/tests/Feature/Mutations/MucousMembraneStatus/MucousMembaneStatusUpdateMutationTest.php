<?php

namespace Tests\Feature\Mutations\MucousMembraneStatus;

use Tests\Helpers\TruncateDatabase;
use App\Models\MucousMembraneStatus;
use Tests\Helpers\PassportSetupTestCase;

class MucousMembraneStatusUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_MucousMembrane_status_update()
	{
		$mucousMembraneStatus = MucousMembraneStatus::factory()->create();
		$updatedMucousMembraneStatus = [
			"name" => "New name",
			"abbreviation" => "MMST",
		];

		$query = '
			mutation MucousMembraneStatusUpdateMutation($id: Int!, $input: mucousMembraneStatusUpdateInput!) {
				mucousMembraneStatusUpdate (id: $id, input: $input) {
					data {
						id          
			}}}';

		$input = [
			"id" => $mucousMembraneStatus->id,
			"input" => [
				"name" => $updatedMucousMembraneStatus["name"],
				"abbreviation" => $updatedMucousMembraneStatus["abbreviation"],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"mucousMembraneStatusUpdate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"mucousMembraneStatusUpdate" => [
						"data" => [
							[
								"id" => "{$mucousMembraneStatus->id}",
							],
						],
					],
				],
			]);

		$query = '
			query MucousMembraneStatusQuery($id: Int!) {
			  mucousMembraneStatuses(id: $id) {
				data {
				  id
				  name
				  abbreviation
				}
			  }
			}
		   ';

		$input = ["id" => $mucousMembraneStatus->id];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200)->assertExactJson([
			"data" => [
				"mucousMembraneStatuses" => [
					"data" => [
						[
							"id" => "{$mucousMembraneStatus->id}",
							"name" => $updatedMucousMembraneStatus["name"],
							"abbreviation" =>
								$updatedMucousMembraneStatus["abbreviation"],
						],
					],
				],
			],
		]);
	}

	public function test_can_not_be_updated_without_label()
	{
		$mucousMembraneStatus = MucousMembraneStatus::factory()->create();

		$query = '
		  mutation MucousMembraneStatusUpdateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"id" => $mucousMembraneStatus->id,
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
		MucousMembraneStatus::factory()->create(["label" => "the label"]);
		$mucousMembraneStatus2 = MucousMembraneStatus::factory()->create();

		$query = '
		  mutation MucousMembraneStatusUpdateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"id" => $mucousMembraneStatus2->id,
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
		$mucousMembraneStatus = MucousMembraneStatus::factory()->create([
			"abbr" => "the abbr",
		]);

		$query = '
		  mutation MucousMembraneStatusUpdateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"id" => $mucousMembraneStatus->id,
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
