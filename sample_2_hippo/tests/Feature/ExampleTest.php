<?php

namespace Tests\Feature;

use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ExampleTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_can_get_patient()
	{
		$query = "{ patients { data { id }}}";
		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"patients" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
	}
}
