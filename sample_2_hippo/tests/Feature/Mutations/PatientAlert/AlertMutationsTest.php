<?php

namespace Tests\Feature\Mutations\PatientAlert;

use App\Models\PatientAlert;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class AlertMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_alert_can_be_created()
	{
		$query = 'mutation PatientAlertCreate($input: patientAlertInput!) {
                    patientAlertCreate(input: $input) {
                        data {
                            id
                        }
                    }
                }';

		$variables = [
			"input" => [
				"description" => "Alert Alert. This is an Alert",
				"addedBy" => "1",
				"patient" => "1",
				"organization" => "3000",
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"patientAlertCreate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"patientAlertCreate" => [
						"data" => [["id" => "1"]],
					],
				],
			]);
	}

	// The application currently has no alert update functionality
	// This test should be added if and when alert update is added
	//	public function test_alert_can_be_updated()
	//  {
	//
	//	}

	public function test_alert_can_be_deleted()
	{
		$alert = PatientAlert::factory()->create();

		$query =
			'
			mutation {
				patientAlertDelete(id: "' .
			$alert->id .
			'") {
					data {
						id
					}
				}
			}';

		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"patientAlertDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$this->assertSoftDeleted("tblPatientAlerts", ["id" => $alert->id]);
	}
}
