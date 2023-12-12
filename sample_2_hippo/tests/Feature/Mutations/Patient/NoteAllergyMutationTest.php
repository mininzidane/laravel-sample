<?php

namespace Tests\Feature\Mutations\Patient;

use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class NoteAllergyMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_can_create_allergy_note()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = 'mutation {
                patientAllergyNoteUpdate (input:
                {
                    note: "This is a note for a thing... that I am doing" ,
                    clientId: 1,
                }) {
                    data {
                        note
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"patientAllergyNoteUpdate" => [
					"data" => [
						"*" => ["note"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"patientAllergyNoteUpdate" => [
					"data" => [
						[
							"note" =>
								"This is a note for a thing... that I am doing",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("tblPatientAllergiesNotes", [
			"client_id" => 1,
			"note" => "This is a note for a thing... that I am doing",
		]);
	}
}
