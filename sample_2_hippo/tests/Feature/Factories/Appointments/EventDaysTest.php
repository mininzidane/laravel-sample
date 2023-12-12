<?php
namespace Tests\Feature\Factories\Appointments;

use App\Models\Appointment;
use App\Models\EventDays;
use App\Models\EventRecur;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class EventDaysTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_day_of_week_abbreviation_attribute()
	{
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

		// Assert that the day of week abbreviation attribute is correct for each event day
		foreach ($eventDays as $eventDay) {
			$this->assertNotNull($eventDay->dayOfWeekAbbreviation);
		}
	}
}
