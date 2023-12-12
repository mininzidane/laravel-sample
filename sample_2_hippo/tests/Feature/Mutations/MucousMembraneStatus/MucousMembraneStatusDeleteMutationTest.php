<?php

namespace Tests\Feature\Mutations\MucousMembraneStatus;

use Tests\Helpers\TruncateDatabase;
use App\Models\MucousMembraneStatus;
use Tests\Helpers\PassportSetupTestCase;

class MucousMembraneStatusDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_mucousMembrane_status_delete()
	{
		$mucousMembraneStatus = MucousMembraneStatus::factory()->create();

		$query = '
		  mutation MucousMembraneStatusDeleteMutation($id: Int!) {
			mucousMembraneStatusDelete(id: $id) {
			  data {
				id
			  }
			}
		  }
		';

		$input = [
			"id" => "{$mucousMembraneStatus->id}",
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"mucousMembraneStatusDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}

	public function test_mucousMembrane_status_can_not_delete_without_id()
	{
		MucousMembraneStatus::factory()->create();

		$query = '
		  mutation MucousMembraneStatusDeleteMutation($id: Int!) {
			mucousMembraneStatusDelete(id: $id) {
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

	public function test_mucousMembrane_status_can_not_delete_without_valid_id()
	{
		MucousMembraneStatus::factory()->create(["id" => 404]);

		$query = '
		  mutation MucousMembraneStatusDeleteMutation($id: Int!) {
			mucousMembraneStatusDelete(id: $id) {
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
			"No query results for model [App\\Models\\MucousMembraneStatus] 401",
			$response->json("*.*.debugMessage"),
		);
	}
}
