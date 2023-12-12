<?php

namespace Tests\Feature\Mutations\Credit;

use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class CreditMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_can_create_credit_with_owner()
	{
		//TODO add owner factory
		$query = 'mutation {
                creditCreate (input: {
                    id: "",
                    number: "234",
                    type: "Gift Card",
                    currentValue: 34,
                    originalValue: 34,
                    updatedAt: "",
                    owner: "1"
                }) {
                    data {
                        id
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"creditCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"creditCreate" => [
					"data" => [
						[
							"id" => "1",
						],
					],
				],
			],
		]);
	}

	public function test_can_create_credit_without_owner()
	{
		//TODO add owner factory
		$query = 'mutation {
                creditCreate (input: {
                    id: "",
                    number: "23455",
                    type: "Gift Card",
                    currentValue: 234,
                    originalValue: 234,
                    updatedAt: ""
                }) {
                    data {
                        id
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"creditCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"creditCreate" => [
					"data" => [
						[
							"id" => "1",
						],
					],
				],
			],
		]);
	}

	public function test_can_update_credit()
	{
		$this->create_credit();

		$query = 'mutation {
                creditUpdate (
                id: "1", 
                input: {
                    id: "1",
                    number: "234",
                    type: "Gift Card",
                    currentValue: 340,
                    originalValue: 34,
                    updatedAt: "2022-09-26 20:04:59"
                }) {
                    data {
                        id
                        currentValue
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"creditUpdate" => [
					"data" => [
						"*" => ["id", "currentValue"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"creditUpdate" => [
					"data" => [
						[
							"id" => "1",
							"currentValue" => 340,
						],
					],
				],
			],
		]);
	}

	public function test_can_delete_credit()
	{
		//TODO add owner factory
		$this->create_credit();

		$query = 'mutation {
                creditDelete(id: 1) {
                    data {
                        id
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"creditDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"creditDelete" => [
					"data" => [],
				],
			],
		]);
	}

	private function create_credit()
	{
		//TODO add owner factory
		$query = 'mutation {
                creditCreate (input: {
                    id: "",
                    number: "234",
                    type: "Gift Card",
                    currentValue: 34,
                    originalValue: 34,
                    updatedAt: "",
                    owner: "1"
                }) {
                    data {
                        id
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);
	}
}
