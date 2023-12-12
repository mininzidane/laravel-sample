<?php

namespace Tests\Feature\Factories\Appointments;

use App\Models\Appointment;
use App\Models\Organization;
use Illuminate\Support\Carbon;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;
use Tests\TestCase;

class AppointmentTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	/**
	 * Test creating a new schedule.
	 *
	 * @return void
	 */
	public function testCreateSchedule()
	{
		// Create a new schedule using the factory
		/** @var Appointment $model */
		$model = Appointment::factory()->create();

		// Assert that the schedule was created and saved to the database
		$this->assertDatabaseHas("tblSchedule", [
			"id" => $model->id,
			"organization_id" => $model->organization_id,
			"user_id" => $model->user_id,
			"creator_id" => $model->creator_id,
			"resource_id" => $model->resource_id,
			"client_id" => $model->client_id,
			"event_id" => $model->event_id,
			"type_id" => $model->type_id,
			"start_time" => $model->start_time->format("Y-m-d H:i:s"),
			"status" => $model->status,
			"duration" => $model->duration,
			"color" => $model->color,
			"removed" => $model->removed,
			"description" => $model->description,
			"name" => $model->name,
			"blocked" => $model->blocked,
			"google_calendar_event_id" => $model->google_calendar_event_id,
			"google_can_edit" => $model->google_can_edit,
			"check_in_time" => $model->check_in_time,
			"check_out_time" => $model->check_out_time,
			"google_message_number" => $model->google_message_number,
			"updated_by" => $model->updated_by,
		]);
	}
}
