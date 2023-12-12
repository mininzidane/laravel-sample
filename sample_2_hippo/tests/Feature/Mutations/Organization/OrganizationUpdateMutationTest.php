<?php

namespace Tests\Feature\Mutations\Organization;

use App\Models\Organization;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;

class OrganizationUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_organization_can_be_updated()
	{
		$organization = Organization::factory()->create();
		$query =
			'
			mutation {
					organizationUpdate (
							id: "' .
			$organization->id .
			'",
							input: {
									id: "' .
			$organization->id .
			'",
									name: "My Practice",
									ein: "1234",
									units: 1,
									currencySymbol: "$",
									phrActive: false,
									vcpActive: false,
									vcpUserName: "",
									vcpPassword: "",
									idexxActive: false,
									idexxUsername: "",
									idexxPassword: "",
									estimateStatement: null,
									paymentInfo: null,
									returnPolicy: null,
									imageName: "2010/logo.png",
									imageUrl: "https://hippo-test-logos.s3.amazonaws.com/2010/logo.png"
							}
					) {
							data {
									id,
									ein,
									units,
									currencySymbol,
									phrActive,
									vcpActive,
									vcpUserName,
									vcpPassword,
									idexxActive,
									idexxUsername,
									idexxPassword,
									estimateStatement,
									paymentInfo,
									returnPolicy,
									imageName,
									imageUrl
							}
					}
			}
		';

		$response = $this->postGraphqlJson($query);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"organizationUpdate" => [
						"data" => [
							"*" => [],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"organizationUpdate" => [
						"data" => [
							[
								"id" => "$organization->id",
								"ein" => "1234",
								"units" => "English",
								"currencySymbol" => "$",
								"phrActive" => false,
								"vcpActive" => false,
								"vcpUserName" => "",
								"vcpPassword" => "",
								"idexxActive" => false,
								"idexxUsername" => "",
								"idexxPassword" => "",
								"estimateStatement" => null,
								"paymentInfo" => null,
								"returnPolicy" => null,
								"imageName" => "2010/logo.png",
								"imageUrl" =>
									"https://hippo-test-logos.s3.amazonaws.com/2010/logo.png",
							],
						],
					],
				],
			]);
	}

	public function test_organization_cannot_be_updated_without_name()
	{
		$organization = Organization::factory()->create();

		$query =
			'
			mutation {
					organizationUpdate (
							id: "' .
			$organization->id .
			'",
							input: {
									id: "' .
			$organization->id .
			'",
									name: "",
									ein: "1234",
									units: 1,
									currencySymbol: "$"
							}
					) {
							data {
									id,
									ein
							}
					}
			}
		';
		$response = $this->postGraphqlJson($query);

		$this->assertContains(
			"Organization name is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_organization_cannot_be_updated_without_ein()
	{
		$organization = Organization::factory()->create();
		$query =
			'
			mutation {
					organizationUpdate (
							id: "' .
			$organization->id .
			'",
							input: {
									id: "' .
			$organization->id .
			'",
									name: "My Practice",
									ein: "",
									units: 1,
									currencySymbol: "$",
							}
					) {
							data {
									id
							}
					}
			}
		';
		$response = $this->postGraphqlJson($query);

		$this->assertContains(
			"EIN is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_organization_cannot_be_updated_without_units()
	{
		$organization = Organization::factory()->create();
		$query =
			'
			mutation {
					organizationUpdate (
							id: "' .
			$organization->id .
			'",
							input: {
									id: "' .
			$organization->id .
			'",
									name: "My Practice",
									ein: "1234",
									units: null,
									currencySymbol: "$"
							}
					) {
							data {
									id
							}
					}
			}
		';
		$response = $this->postGraphqlJson($query);

		$this->assertContains(
			"Measurement units are required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_organization_cannot_be_updated_without_currency()
	{
		$organization = Organization::factory()->create();
		$query =
			'
			mutation {
					organizationUpdate (
							id: "' .
			$organization->id .
			'",
							input: {
									id: "' .
			$organization->id .
			'",
									name: "My Practice",
									ein: "1234",
									units: 1,
									currencySymbol: ""
							}
					) {
							data {
									id
							}
					}
			}
		';
		$response = $this->postGraphqlJson($query);

		$this->assertContains(
			"Currency is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_organization_cannot_be_updated_without_existing_id()
	{
		$query = '
			mutation {
					organizationUpdate (
							id: "666",
							input: {
									id: "666",
									name: "My Practice",
									ein: "1234",
									units: 1,
									currencySymbol: "$",
							}
					) {
							data {
									id
							}
					}
			}
		';
		$response = $this->postGraphqlJson($query);
		$this->assertContains(
			"Cannot update non-existent organization: 666",
			$response->json("errors.*.debugMessage"),
		);
	}
}
