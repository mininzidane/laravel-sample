<?php
namespace Tests\Feature\Query\AppointmentStatus;

use App\Models\Appointment;
use App\Models\EventDays;
use App\Models\EventRecur;
use Illuminate\Support\Carbon;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class RepeatsOnQueryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function testScratchQuery()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);
		// Create an appointment
		$appointment = Appointment::factory()->create();

		// Create an event recur for the appointment
		$eventRecur = EventRecur::factory()->create([
			"schedule_event_id" => $appointment->id,
		]);

		// Create some event days for the event recur
		$eventDays = EventDays::factory()
			->count(5)
			->create([
				"event_id" => $appointment->id,
			]);

		$query = 'query ScratchQuery {
            eventRecur (subdomain: "hippodb_test", page:1 limit:1000) {
                data {
                    appointment {
                        id
                        recur {appointment{ id }}
                    }
                    id
                    repeats
                    repeatsEvery
                    repeatBy
                    startDate
                    endType
                    endDate
                    endOn
                    rrule
                    skips {id, time}
                    repeatsOn {day, dayOfWeekAbbreviation}
                    createdAt
                    updatedAt
                }
            }
        }';

		$response = $this->postGraphqlJson($query);

		$response->assertJson([
			"data" => [
				"eventRecur" => [
					"data" => [
						[
							"appointment" => [
								"id" => (string) $appointment->id,
								"recur" => [
									"appointment" => [
										"id" => (string) $eventRecur->id,
									],
								],
							],
							"id" => (string) $eventRecur->id,
							"repeats" => $eventRecur->repeats,
							"repeatsEvery" => $eventRecur->repeats_every,
							"repeatBy" => $eventRecur->repeat_by,
							"startDate" => $eventRecur->start_date,
							"endType" => $eventRecur->end_type,
							"endDate" => $eventRecur->end_date,
							"endOn" => $eventRecur->end_on,
							"rrule" => $eventRecur->rrule,
							"skips" => [],
							"repeatsOn" => $eventRecur->repeatsOn
								->map(function ($repeatsOn) {
									return [
										"day" => $repeatsOn->day,
										"dayOfWeekAbbreviation" =>
											$repeatsOn->day_of_week_abbreviation,
									];
								})
								->toArray(),
							"createdAt" => $eventRecur->created_at->format(
								"Y-m-d H:i:s",
							),
							"updatedAt" => $eventRecur->updated_at->format(
								"Y-m-d H:i:s",
							),
						],
					],
				],
			],
		]);
	}
}
