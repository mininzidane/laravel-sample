<?php

namespace Tests\Feature\Mutations\Species;

use App\Models\Patient;
use App\Models\Species;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class SpeciesDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected $query = '
      mutation SpeciesDeleteMutation($id: Int!) {
        speciesDelete(id: $id) {
          data {
            id
          }
        }
      }
    ';

	public function test_species_can_be_deleted()
	{
		$species = Species::factory()->create();

		$variables = [
			"id" => intval($species["id"]),
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"speciesDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$this->assertSoftDeleted("tblSpecies", ["id" => $species->id]);
	}

	// Protection against species deletion when species definition has associated patients
	// is done by App checking patientCount returned by API
	public function test_breed_cannot_be_deleted_when_referenced()
	{
		$patient = Patient::factory()->create();

		$patientCountQuery = '
			query SpeciesRelationshipCount($id: Int!) {
			 species(id: $id) {
			   data {
				 relationshipNumber
			   }
			 }
			}';

		$response = $this->postGraphqlJsonWithVariables($patientCountQuery, [
			"id" => $patient->species_id,
		]);

		$response->assertStatus(200)->assertExactJson([
			"data" => [
				"species" => [
					"data" => [
						0 => [
							"relationshipNumber" => "1",
						],
					],
				],
			],
		]);
	}
}
