<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Location;

use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LocationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Location $location */
		$location = Location::factory()->create();

		$this->assertDatabaseHas($location->getTable(), [
			"id" => $location->id,
			"organization_id" => $location->organization_id,
			"name" => $location->name,
			"address1" => $location->address1,
			"address2" => $location->address2,
			"address3" => $location->address3,
			"city" => $location->city,
			"state" => $location->state,
			"zip" => $location->zip,
			"country" => $location->country,
			"timezone" => $location->timezone,
			"phone1" => $location->phone1,
			"phone2" => $location->phone2,
			"phone3" => $location->phone3,
			"fax" => $location->fax,
			"email" => $location->email,
			"email_identity_verified" => $location->email_identity_verified,
			"public_domain_email" => $location->public_domain_email,
			"primary" => $location->primary,
			"removed" => $location->removed,
			"image_name" => $location->image_name,
			"google_calendar_access_token" =>
				$location->google_calendar_access_token,
			"google_calendar_refresh_token" =>
				$location->google_calendar_refresh_token,
			"google_sync" => $location->google_sync,
			"google_confirm" => $location->google_confirm,
			"google_token" => $location->google_token,
		]);
	}
}
