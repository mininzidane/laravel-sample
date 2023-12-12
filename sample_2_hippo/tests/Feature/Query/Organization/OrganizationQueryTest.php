<?php

namespace Tests\Feature\Query\Organization;

use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class OrganizationQueryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_organization_query()
	{
		$query = '
			query {
				organizations {
					data {
						id
						name
						ein
						units
						currencySymbol
						phrActive
						vcpActive
						vcpUserName
						vcpPassword
						idexxActive
						idexxUsername
						idexxPassword
						estimateStatement
						paymentInfo
						returnPolicy
						imageName
						imageUrl
					}
				}
			}
		';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"organizations" => [
					"data" => [
						"*" => ["id", "name", "ein", "units", "currencySymbol"],
					],
				],
			],
		]);
		// There should only ever be 1 organization record
		$response->assertJsonCount(1, "data.organizations.data");
	}
}
