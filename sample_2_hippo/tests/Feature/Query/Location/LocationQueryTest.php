<?php

namespace Tests\Feature\Query\Location;

use App\Models\Location;
use App\Models\LocationSetting;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class LocationQueryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_locations_query_limit()
	{
		$query = '
      query {
        locations(limit: 1) {
          current_page
          per_page
          total
          data {
						id
						name
						address1
						address2
						address3
						city
						zip
						subregion {
							id
							name
						}
						tz {
							id
							name
							offset
							php_supported
						}
						phone1
						phone2
						phone3
						fax
						imageName
						imageUrl
						primary
						email
						emailVerified
						autoSave
						antechActive
						antechAccountId
						antechClinicId
						antechUsername
						antechPassword
						zoetisActive
						zoetisFuseId
    
          }
        }
      }
    ';
		$response = $this->postGraphqlJson($query);
		$response->assertJsonStructure([
			"data" => [
				"locations" => [
					"data" => [
						"*" => ["id", "name", "primary", "email"],
					],
				],
			],
		]);
		$response->assertJsonCount(1, "data.locations.data");
	}

	public function test_locations_query_by_id()
	{
		$location = Location::factory()->create([
			"name" => "Test Location Hotel Indigo",
			"address1" => "123 Four Street",
			"city" => "Tiny Town",
			"email" => "hippnotize@example.com",
			"primary" => false,
		]);
		$query =
			'
      query {
        locations(id: ' .
			$location->id .
			') {
          data {
						id
						name
						address1
						city
						email
						primary
          }
        }
      }
    ';
		$response = $this->postGraphqlJson($query);
		$response->assertJsonStructure([
			"data" => [
				"locations" => [
					"data" => [
						"*" => [
							"id",
							"name",
							"address1",
							"city",
							"primary",
							"email",
						],
					],
				],
			],
		]);
		$response->assertJsonCount(1, "data.locations.data");
		$response->assertExactJson([
			"data" => [
				"locations" => [
					"data" => [
						[
							"id" => "{$location->id}",
							"name" => "Test Location Hotel Indigo",
							"address1" => "123 Four Street",
							"city" => "Tiny Town",
							"primary" => false,
							"email" => "hippnotize@example.com",
						],
					],
				],
			],
		]);
	}

	public function test_locations_query_antech_password_false()
	{
		$location = Location::factory()->create();
		$locationSettings = LocationSetting::factory()->create([
			"location_id" => $location->id,
			"setting_name" => "antech-client-password",
			"setting_value" => "",
		]);

		$query =
			'
      query {
        locations(id: ' .
			$location->id .
			') {
          data {
						id
						name
						address1
						city
						email
						primary
						antechPasswordHas
          }
        }
      }
    ';
		$response = $this->postGraphqlJson($query);
		$response
			->assertJsonStructure([
				"data" => [
					"locations" => [
						"data" => [
							"*" => [
								"id",
								"name",
								"address1",
								"city",
								"primary",
								"email",
								"antechPasswordHas",
							],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"locations" => [
						"data" => [
							[
								"id" => "$location->id",
								"name" => "$location->name",
								"address1" => "$location->address1",
								"city" => "$location->city",
								"primary" => $location->primary,
								"email" => "$location->email",
								"antechPasswordHas" => false,
							],
						],
					],
				],
			]);
	}
	public function test_locations_query_antech_password_true()
	{
		$location = Location::factory()->create();
		$locationSettings = LocationSetting::factory()->create([
			"location_id" => $location->id,
			"setting_name" => "antech-client-password",
			"setting_value" => "password",
		]);

		$query =
			'
      query {
        locations(id: ' .
			$location->id .
			') {
          data {
						id
						name
						address1
						city
						email
						primary
						antechPasswordHas
          }
        }
      }
    ';
		$response = $this->postGraphqlJson($query);
		$response
			->assertJsonStructure([
				"data" => [
					"locations" => [
						"data" => [
							"*" => [
								"id",
								"name",
								"address1",
								"city",
								"primary",
								"email",
								"antechPasswordHas",
							],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"locations" => [
						"data" => [
							[
								"id" => "$location->id",
								"name" => "$location->name",
								"address1" => "$location->address1",
								"city" => "$location->city",
								"primary" => $location->primary,
								"email" => "$location->email",
								"antechPasswordHas" => true,
							],
						],
					],
				],
			]);
	}
}
