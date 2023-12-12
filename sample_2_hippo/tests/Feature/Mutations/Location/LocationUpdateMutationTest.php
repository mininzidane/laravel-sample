<?php

namespace Tests\Feature\Mutations\Location;

use App\Models\Location;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class LocationUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_location_can_be_updated()
	{
		$location = Location::factory()->create();
		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			'
          input: {
            organizationId: 3000,
            name: "Test Location Alpha",
            primary: false,
            address1: "123 Any Street",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone1: "",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
            email: "",
            autoSave: true,
            antechActive: false,
            antechAccountId: "",
            antechClinicId: "",
            antechUsername: "",
            antechPassword: "",
            zoetisActive: false,
            zoetisFuseId: "",
            imageName: ""
          }
        )
        {
          data {
          	id,
            name,
            primary,
            address1,
            address2,
            address3,
            city,
            subregion {
            	name
            },
            zip,
            phone1,
            phone2,
            phone3,
            fax,
            tz {
            	name
            },
            email,
            emailVerified,
            autoSave,
            antechActive,
            antechAccountId,
            antechClinicId,
            antechUsername,
            antechPassword,
            zoetisActive,
            zoetisFuseId,
            imageName
          }

        }
      }
		';

		$response = $this->postGraphqlJson($query);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"locationUpdate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"locationUpdate" => [
						"data" => [
							[
								"id" => "{$location->id}",
								"name" => "Test Location Alpha",
								"primary" => false,
								"address1" => "123 Any Street",
								"address2" => "",
								"address3" => "",
								"city" => "Bossville",
								"subregion" => [
									"name" => "California",
								],
								"zip" => "90210",
								"phone1" => "",
								"phone2" => "",
								"phone3" => "",
								"fax" => "",
								"tz" => [
									"name" => "Pacific Standard Time",
								],
								"email" => "",
								"emailVerified" => false,
								"autoSave" => true,
								"antechActive" => false,
								"antechAccountId" => "",
								"antechClinicId" => "",
								"antechUsername" => "",
								"antechPassword" => "",
								"zoetisActive" => false,
								"zoetisFuseId" => "",
								"imageName" => "",
							],
						],
					],
				],
			]);
	}

	public function test_location_cannot_be_updated_without_name()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
          input: {
            organizationId: 3000,
            name: "",
            primary: false,
            address1: "123 Any Street",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone1: "",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
            email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Location name is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_name()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
          input: {
            organizationId: 3000,
            name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
            primary: false,
            address1: "123 Any Street",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone1: "",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
            email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"The input.name may not be greater than 100 characters.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_address1()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
          input: {
            organizationId: 3000,
            name: "Hot Diggity Dog",
            primary: false,
            address1: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone1: "",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
            email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Address1 must be smaller than 100 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_address2()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
          input: {
            organizationId: 3000,
            name: "Hot Diggity Dog",
            primary: false,
            address2: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
            address1: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone1: "",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
            email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Address2 must be smaller than 100 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_address3()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
					input: {
							organizationId: 3000,
							name: "Hot Diggity Dog",
							primary: false,
							address3: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
							address2: "",
							address1: "",
							city: "Bossville",
							state: 3684,
							zip: "90210",
							phone1: "",
							phone2: "",
							phone3: "",
							fax: "",
							timezone: 6,
							email: "info@hippomanager.com",
						}
					)
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Address3 must be smaller than 100 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_city()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
					input: {
						organizationId: 3000,
						name: "Hot Diggity Dog",
						primary: false,
						city: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
						address2: "",
						address3: "",
						state: 3684,
						zip: "90210",
						phone1: "",
						phone2: "",
						phone3: "",
						fax: "",
						timezone: 6,
						email: "info@hippomanager.com",
					}
				)
			{
			data {
				id
			}
		}
	}';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"City must be smaller than 70 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_zip()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
			 	input: {
					organizationId: 3000,
					name: "Hot Diggity Dog",
					primary: false,
					zip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
					address2: "",
					address3: "",
					city: "Bossville",
					state: 3684,
					phone1: "",
					phone2: "",
					phone3: "",
					fax: "",
					timezone: 6,
					email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Zip must be smaller than 16 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_phone1()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
					input: {
						organizationId: 3000,
						name: "Hot Diggity Dog",
						primary: false,
						phone1: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
						address2: "",
						address3: "",
						city: "Bossville",
						state: 3684,
						zip: "90210",
						phone2: "",
						phone3: "",
						fax: "",
						timezone: 6,
						email: "info@hippomanager.com",
					}
				)
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Phone1 must be smaller than 16 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_phone2()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
				input: {
					organizationId: 3000,
					name: "Hot Diggity Dog",
					primary: false,
					phone2: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
					address2: "",
					address3: "",
					city: "Bossville",
					state: 3684,
					zip: "90210",
					timezone: 6,
					email: "info@hippomanager.com",
				}
			)
			{
				data {
					id
				}
			}
		}
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Phone2 must be smaller than 16 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_phone3()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
					input: {
						organizationId: 3000,
						name: "Hot Diggity Dog",
						primary: false,
						phone3: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
						address2: "",
						address3: "",
						city: "Bossville",
						state: 3684,
						zip: "90210",
						fax: "",
						timezone: 6
					}
				)
				{
					data {
						id
					}
				}
			}
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Phone3 must be smaller than 16 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_fax()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
					input: {
            organizationId: 3000,
            name: "Hot Diggity Dog",
            primary: false,
            fax: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            timezone: 6,
            email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Fax must be smaller than 16 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_long_email()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        	id: ' .
			$location->id .
			',
					input: {
            organizationId: 3000,
            name: "Hot Diggity Dog",
            primary: false,
            email: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor ante vel justo sagittis, eget ultricies turpis condimentum. Fusce id justo vitae elit pharetra faucibus ut et mauris. Sed in enim purus. Quisque lobortis malesuada justo non fringilla.",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Email must be smaller than 255 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_cannot_be_updated_with_duplicate_keys()
	{
		$query = '
			mutation {
        locationCreate (
          input: {
            organizationId: 3000,
            name: "Big Time Dogs & Cats",
            name: "Big Time Dogs & Cats",
            primary: false,
            address1: "123 Any Street",
            address2: "",
            address3: "",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            phone1: "",
            phone2: "",
            phone3: "",
            fax: "",
            timezone: 6,
            email: "info@hippomanager.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"There can be only one input field named \"name\".",
			$response->json("errors.*.message"),
		);
	}

	public function test_location_email_change_does_not_verify_email()
	{
		$location = Location::factory()->create([
			"id" => 13,
			"email" => "test@example.com",
			"email_identity_verified" => 0,
			"public_domain_email" => 0,
		]);
		$this->assertDatabaseHas($location->table, [
			"id" => $location->id,
			"email" => $location->email,
			"email_identity_verified" => 0,
			"public_domain_email" => 0,
		]);

		$query =
			'
							mutation {
								locationUpdate (
									id: ' .
			$location->id .
			',
									input: {
										organizationId: ' .
			$location->organization_id .
			',
										name: "' .
			$location->name .
			'",
					timezone: ' .
			$location->timezone .
			',
										email: "",
									}
								)
								{
									data {
										id
									}
								}
							}
						';
		$response = $this->postGraphqlJson($query)->assertStatus(200);
		$this->assertDatabaseHas($location->table, [
			"id" => $location->id,
			"email" => "",
			"email_identity_verified" => 0,
			"public_domain_email" => 0,
		]);
	}
	public function test_location_with_email_has_verified_and_public_set_correctly()
	{
		$location = Location::factory()->create();

		$query =
			'
			mutation {
        locationUpdate (
        id: ' .
			$location->id .
			',
          input: {
            organizationId: 3000,
            name: "Big Time Dogs & Cats",
            primary: false,
            address1: "123 Any Street",
            city: "Bossville",
            state: 3684,
            zip: "90210",
            timezone: 6,
            email: "steve@apple.com",
          }
        )
        {
          data {
						id
          }
        }
      }
		';
		$response = $this->postGraphqlJson($query);
		$this->assertDatabaseHas($location->table, [
			"email" => "steve@apple.com",
			"email_identity_verified" => 0,
			"public_domain_email" => 1,
		]);
	}
}
