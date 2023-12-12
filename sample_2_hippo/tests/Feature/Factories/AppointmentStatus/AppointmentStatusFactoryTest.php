<?php

namespace Tests\Feature\Factories\AppointmentStatus;

use App\Models\AppointmentStatus;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class AppointmentStatusFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$status = AppointmentStatus::factory()->create();

		$this->assertDatabaseHas("tblAppointmentStatuses", [
			"status_key" => $status->status_key,
			"status_name" => $status->status_name,
			"in_hospital_status" => $status->in_hospital_status,
			"last_visit_status" => $status->last_visit_status,
			"default_status" => $status->default_status,
			"hidden" => $status->hidden,
			"check_out_status" => $status->check_out_status,
			"sale_complete_default_status" =>
				$status->sale_complete_default_status,
		]);
	}
}
