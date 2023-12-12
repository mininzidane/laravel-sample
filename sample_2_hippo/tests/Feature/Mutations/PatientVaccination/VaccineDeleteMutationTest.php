<?php

namespace Tests\Feature\Mutations\PatientVaccination;

use App\Models\Vaccination;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class PatientVaccinationDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_a_vaccine_can_be_deleted()
	{
		$vaccine = Vaccination::factory()->create();

		$query =
			'
			mutation {
			  patientVaccinationDelete(id: ' .
			$vaccine->id .
			') {
				data {
				  id
				}
			  }
			}';

		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200);

		$this->assertSoftDeleted("tblPatientVaccines", [
			"id" => "{$vaccine->id}",
		]);
	}
}
