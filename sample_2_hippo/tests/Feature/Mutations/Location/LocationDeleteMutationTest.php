<?php

namespace Tests\Feature\Mutations\Location;

use App\Models\Location;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class LocationDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_location_can_be_deleted()
	{
		$location = Location::factory()->create();
		$query =
			'
      mutation {
        locationDelete ( id: "' .
			$location->id .
			'" ) {
          data {
            id
          }
        }
      }';

		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200)->assertJsonStructure([
			"data" => [
				"locationDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$this->assertSoftDeleted($location);
	}
}
