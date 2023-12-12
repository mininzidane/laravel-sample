<?php

namespace Tests\Feature\Mutations\Breed;

use App\Models\Breed;
use App\Models\Patient;
use App\Models\PatientBreed;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class BreedDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected $query = '
        mutation BreedDeleteMutation($id: Int!) {
            breedDelete(id: $id) {
                data {
                    id
                }
            }
        }
    ';

	public function test_breed_can_be_deleted()
	{
		$breed = $this->create_breed();

		$variables = [
			"id" => intval($breed["id"]),
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"breedDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}

	// Protection against breed deletion when breed definition has associated patients
	// is done by App checking patientCount returned by API
	public function test_breed_cannot_be_deleted_when_referenced()
	{
		$breed = Breed::factory()->create();
		$patient = Patient::factory()->create();
		PatientBreed::factory()->create([
			"client_id" => $patient->id,
			"breed" => $breed->name,
		]);

		$patientCountQuery = '
      		query BreedPatientCount($id: Int!) {
	      		breeds(id: $id) {
	      			data {
	      				name
	      				species
	        			patientCount
	     			}
	    		}
	   		}';

		$response = $this->postGraphqlJsonWithVariables($patientCountQuery, [
			"id" => $breed->id,
		]);

		$response->assertStatus(200)->assertExactJson([
			"data" => [
				"breeds" => [
					"data" => [
						0 => [
							"name" => $breed->name,
							"patientCount" => "1",
							"species" => $breed->species,
						],
					],
				],
			],
		]);
	}
}
