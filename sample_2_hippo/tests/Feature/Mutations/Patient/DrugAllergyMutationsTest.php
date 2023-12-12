<?php

namespace Tests\Feature\Mutations\Patient;

use App\Models\Allergy;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class DrugAllergyMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_can_create_drug_allergies()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = 'mutation {
                patientDrugAllergiesCreate (input:[
                    {
                        allergy: "Codeine",
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
				"patientDrugAllergiesCreate" => [
					"data" => [
						"*" => ["id", "allergy", "updatedAt"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientDrugAllergiesCreate" => [
					"data" => [
						[
							"id" => "1",
							"allergy" => "Codeine",
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientDrugAllergies", [
			"id" => 1,
			"client_id" => 1,
			"allergy" => "Codeine",
		]);
	}

	public function test_can_create_multiple_drug_allergies()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = 'mutation {
                patientDrugAllergiesCreate (input:[
                    {
                        allergy: "Codeine",
                        clientId: 1
                    },
                    {
                        allergy: "Penicillin",
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
				"patientDrugAllergiesCreate" => [
					"data" => [
						"*" => ["id", "allergy", "updatedAt"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientDrugAllergiesCreate" => [
					"data" => [
						[
							"id" => "1",
							"allergy" => "Codeine",
							"updatedAt" => "2022-05-21 12:00:00",
						],
						[
							"id" => "2",
							"allergy" => "Penicillin",
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientDrugAllergies", [
			"client_id" => 1,
			"allergy" => "Codeine",
		])->assertDatabaseHas("tblPatientDrugAllergies", [
			"client_id" => 1,
			"allergy" => "Penicillin",
		]);
	}

	public function test_can_update_multiple_drug_allergies()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$this->drug_allergy_create();

		$query = 'mutation {
                patientDrugAllergiesCreate (input:[
                    {
                        allergy: "Phenytoin",
                        clientId: 1
                    },
                    {
                        allergy: "Penicillin",
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
				"patientDrugAllergiesCreate" => [
					"data" => [
						"*" => ["id", "allergy", "updatedAt"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientDrugAllergiesCreate" => [
					"data" => [
						[
							"id" => "2",
							"allergy" => "Phenytoin",
							"updatedAt" => "2022-05-21 12:00:00",
						],
						[
							"id" => "3",
							"allergy" => "Penicillin",
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientDrugAllergies", [
			"client_id" => 1,
			"allergy" => "Codeine",
		])
			->assertDatabaseHas("tblPatientDrugAllergies", [
				"client_id" => 1,
				"allergy" => "Penicillin",
			])
			->assertDatabaseHas("tblPatientDrugAllergies", [
				"client_id" => 1,
				"allergy" => "Phenytoin",
			]);
	}

	public function test_can_delete_drug_allergy()
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
