<?php

namespace Tests\Feature\Factories\Organization;

use App\Models\Organization;
use Illuminate\Support\Facades\Storage;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class OrganizationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Organization $organization */
		$organization = Organization::factory()->create();

		$this->assertDatabaseHas("tblOrganizations", [
			"id" => $organization->id,
			"name" => $organization->name,
			"email" => $organization->email,
			"medicare_id" => $organization->medicare_id,
			"medicade_id" => $organization->medicade_id,
			"champus_id" => $organization->champus_id,
			"npi" => $organization->npi,
			"ein" => $organization->ein,
			"upin" => $organization->upin,
			"partner" => $organization->partner,
			"salesman" => $organization->salesman,
			"units" => $organization->units,
			"image_name" => $organization->image_name,
			"phr_setup_status" => $organization->phr_setup_status,
			"phr_active" => $organization->phr_active,
			"postcards_setup_status" => $organization->postcards_setup_status,
			"postcards_active" => $organization->postcards_active,
			"google_calendar_setup_status" =>
				$organization->google_calendar_setup_status,
			"google_calendar_active" => $organization->google_calendar_active,
		]);

		// Check that appends attribute is set
		$this->assertEquals(
			Storage::disk("s3-logos")->url($organization->image_name),
			$organization->image_url,
		);
		$this->assertEquals(null, $organization->payment_info);
	}
}
