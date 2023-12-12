<?php

namespace Tests\Feature\Mutations\PatientVaccination;

use App\Models\Vaccination;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class PatientVaccinationUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected $query = '
		mutation PatientVaccinationUpdate($input: patientVaccinationUpdateInput!) {
			patientVaccinationUpdate (input: $input) {
				data {
					id
					name
					dosage
					administeredAt
					currentGender
					currentWeight
					lotNumber
					serialNumber
					expirationDate
					item {
						id
						name
						isSerialized
					}
					invoice {
						id
						createdAt
					}
					invoiceItem {
						id
					}
					administeredBy {
						id
						firstName
						lastName
						fullName
					}					
					provider {
						id
						firstName
						lastName
						fullName
					}
					locationAdministered {
						id
						name
					}
				}
			}
		}';

	public function test_a_vaccine_can_be_updated()
	{
		// A vaccine cannot be initially created with lot and expiration date
		// This data can only be added on the Edit form
		$vaccine = Vaccination::factory()->create([
			"receiving_item_lot_number" => null,
			"receiving_item_expiration_date" => null,
		]);

		$variables = [
			"input" => [
				"id" => "{$vaccine->id}",
				"invoice" => $vaccine->invoice_id,
				"dosage" => 1,
				"processReminders" => true,
				"allowExcessiveQuantity" => true,
				"location" => $vaccine->location_administered,
				"administeredLocationId" => $vaccine->location_administered,
				"administeredBy" => $vaccine->administered_by,
				"administeredDate" => $vaccine->administered_date->format(
					"Y-m-d",
				),
				"lotNumber" => "A1634",
				"expirationDate" => $this->carbonTestTime->format("Y-m-d"),
			],
		];

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$variables,
		);

		$response->assertStatus(200);

		$this->assertDatabaseHas("tblPatientVaccines", [
			"receiving_item_lot_number" => "A1634",
			"receiving_item_expiration_date" => $this->carbonTestTime->format(
				"Y-m-d",
			),
		]);
	}
}
