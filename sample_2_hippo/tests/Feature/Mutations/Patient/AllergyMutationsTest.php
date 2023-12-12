<?php

namespace Tests\Feature\Mutations\Patient;

use App\Models\Allergy;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportArrangeTestCase;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class AllergyMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_can_create_allergies()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$allergy = Allergy::factory()->create();

		$query =
			'mutation {
                patientAllergiesCreate (input:[
                    {
                        allergy: "' .
			$allergy->name .
			'",
                        clientId: 1
                    }
                ])
                {
                    data {
                        id
                        allergy
                        updatedAt
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"patientAllergiesCreate" => [
					"data" => [
						"*" => ["id", "allergy", "updatedAt"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientAllergiesCreate" => [
					"data" => [
						[
							"id" => "1",
							"allergy" => $allergy->name,
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientAllergies", [
			"id" => 1,
			"client_id" => 1,
			"allergy" => $allergy->name,
		]);
	}

	public function test_can_create_multiple_allergies()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);
		$allergies = Allergy::factory()
			->count(2)
			->create();

		$query =
			'mutation {
                patientAllergiesCreate (input:[
                    {
                        allergy: "' .
			$allergies[0]->name .
			'",
                        clientId: 1
                    },
                    {
                        allergy: "' .
			$allergies[1]->name .
			'",
                        clientId: 1
                    }
                ])
                {
                    data {
                        id
                        allergy
                        updatedAt
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"patientAllergiesCreate" => [
					"data" => [
						"*" => ["id", "allergy", "updatedAt"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientAllergiesCreate" => [
					"data" => [
						[
							"id" => "1",
							"allergy" => $allergies[0]->name,
							"updatedAt" => "2022-05-21 12:00:00",
						],
						[
							"id" => "2",
							"allergy" => $allergies[1]->name,
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientAllergies", [
			"client_id" => 1,
			"allergy" => $allergies[0]->name,
		])->assertDatabaseHas("tblPatientAllergies", [
			"client_id" => 1,
			"allergy" => $allergies[1]->name,
		]);
	}

	public function test_can_delete_allergy()
	{
		// This can get cleaned up after PatientAllergy model factory is created
		Allergy::factory()->create(["name" => "Fabrics"]);
		Allergy::factory()->create(["name" => "Feathers"]);

		$this->create_multiple_allergies();

		$query = 'mutation {
                patientAllergyDelete(id: "2") {
                    data {
                        id
                        allergy
                        updatedAt
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"patientAllergyDelete" => [
					"data" => [
						"*" => ["id", "allergy", "updatedAt"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientAllergyDelete" => [
					"data" => [
						[
							"id" => "1",
							"allergy" => "Fabrics",
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientAllergies", [
			"id" => 1,
			"client_id" => 1,
			"allergy" => "Fabrics",
		]);
	}
}
