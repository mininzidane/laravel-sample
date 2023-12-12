<?php

namespace Tests\Feature\Mutations\MucousMembraneStatus;

use Tests\Helpers\TruncateDatabase;
use App\Models\MucousMembraneStatus;
use Tests\Helpers\PassportSetupTestCase;

class MucousMembraneStatusCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_MucousMembrane_status_creation()
	{
		$MucousMembraneStatus = [
			"label" => "MMS_STATUS_TEST",
			"abbr" => "MMST",
		];

		$query = '
		  mutation MMStatusOptionCreateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
    	';

		$input = [
			"input" => [
				"name" => $MucousMembraneStatus["label"],
				"abbreviation" => $MucousMembraneStatus["abbr"],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"mucousMembraneStatusCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}

	public function test_can_not_be_created_without_label()
	{
		$MucousMembraneStatus = ["label" => null, "abbr" => "MMST"];

		$query = '
		  mutation MMStatusOptionCreateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"input" => [
				"name" => $MucousMembraneStatus["label"],
				"abbreviation" => $MucousMembraneStatus["abbr"],
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
		MucousMembraneStatus::factory()->create(["label" => "the label"]);
		$newMucousMembraneStatus = ["label" => "the label", "abbr" => "MMST"];

		$query = '
		  mutation MMStatusOptionCreateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"input" => [
				"name" => $newMucousMembraneStatus["label"],
				"abbreviation" => $newMucousMembraneStatus["abbr"],
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
		MucousMembraneStatus::factory()->create(["abbr" => "the abbr"]);
		$newMucousMembraneStatus = [
			"label" => "the label",
			"abbr" => "the abbr",
		];

		$query = '
		  mutation MMStatusOptionCreateMutation($input: mucousMembraneStatusCreateInput!) {
			mucousMembraneStatusCreate (input: $input) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"input" => [
				"abbreviation" => $newMucousMembraneStatus["abbr"],
				"name" => $newMucousMembraneStatus["label"],
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
