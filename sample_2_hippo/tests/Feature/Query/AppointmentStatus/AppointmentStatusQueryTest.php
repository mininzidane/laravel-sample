<?php

namespace Tests\Feature\Query\AppointmentStatus;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use Illuminate\Support\Carbon;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportArrangeTestCase;

class AppointmentStatusQueryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_appointment_status_can_have_associated_appointments()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$statusName = "Checked Out";
		$statusKey = "chkout";
		/** @var AppointmentStatus $appointmentStatus */
		$appointmentStatus = AppointmentStatus::factory()->create([
			"status_key" => $statusKey,
			"status_name" => $statusName,
		]);
		/** @var Appointment $appointment */
		$appointment = Appointment::factory()->create(["status" => $statusKey]);

		$query = 'query appointmentStatusesQuery($statusName: String!) {
                  	appointmentStatuses(name: $statusName) {
                  		data {
                  			name
                  			pretty
                  			inHospital
                  			lastVisit
                  			defaultStatus
                  			hidden
                  			appointments {
                  				description
                  				startTime
                  			}
                  		}
                  	}
                  }';

		$variables = [
			"statusName" => $statusName,
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"appointmentStatuses" => [
					"data" => [
						"*" => [
							"name",
							"pretty",
							"inHospital",
							"lastVisit",
							"defaultStatus",
							"hidden",
							"appointments",
						],
					],
				],
			],
		]);

		$response->assertStatus(200)->assertExactJson([
			"data" => [
				"appointmentStatuses" => [
					"data" => [
						[
							"name" => $appointmentStatus->status_key,
							"pretty" => $appointmentStatus->status_name,
							"inHospital" =>
								$appointmentStatus->in_hospital_status,
							"lastVisit" =>
								$appointmentStatus->last_visit_status,
							"defaultStatus" =>
								$appointmentStatus->default_status,
							"hidden" => $appointmentStatus->hidden,
							"appointments" => [
								[
									"description" => $appointment->description,
									"startTime" => $appointment->start_time->format(
										"Y-m-d H:i:s",
									),
								],
							],
						],
					],
				],
			],
		]);
	}
}
