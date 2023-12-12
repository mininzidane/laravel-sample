<?php
namespace Tests\Feature\Factories\Appointments;

use App\Models\Appointment;
use App\Models\EventRecur;
use Illuminate\Support\Carbon;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class EventRecurTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	/** @test */
	public function it_creates_an_event_recur()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$eventRecur = EventRecur::factory()->create();

		// Assert that the event recur was created successfully
		// You can check any fields you want here
		$this->assertDatabaseHas("tblSchedulerEventRecur", [
			"id" => $eventRecur->id,
			"schedule_event_id" => $eventRecur->schedule_event_id,
			"repeats" => $eventRecur->repeats,
			"repeats_every" => $eventRecur->repeats_every,
			"repeat_by" => $eventRecur->repeat_by,
			"start_date" => $eventRecur->start_date,
			"end_type" => $eventRecur->end_type,
			"end_date" => $eventRecur->end_date,
			"end_on" => $eventRecur->end_on,
			"created_at" => "2022-05-21 12:00:00",
			"updated_at" => "2022-05-21 12:00:00",
		]);
	}
}
