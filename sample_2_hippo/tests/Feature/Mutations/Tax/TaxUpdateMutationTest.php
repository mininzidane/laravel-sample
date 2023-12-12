<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Tax;

use App\Models\Tax;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class TaxUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = 'mutation TaxUpdateMutation($id: String!, $name: String, $percent: Float) {
              taxUpdate (id: $id, input: {
                  id: $id,
                  name: $name,
                  percent: $percent
              }) {
                  data {
                      id,
                      percent
                  }
              }
          }
    ';

	public function test_can_update_tax(): void
	{
		Tax::factory()->create();
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => "1",
			"name" => "test name",
			"percent" => 10,
		]);

		$response->assertJsonStructure([
			"data" => [
				"taxUpdate" => [
					"data" => [
						"*" => ["id", "percent"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"taxUpdate" => [
					"data" => [
						[
							"id" => "1",
							"percent" => 10,
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("taxes", [
			"id" => 1,
			"name" => "test name",
			"percent" => 10,
		]);
	}

	public function test_update_not_found_model(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => "1",
			"name" => "test name",
			"percent" => 10,
		]);
		$this->assertContains(
			"Cannot edit non-existent item: 1",
			$response->json("errors.*.errorMessage"),
		);
	}

	public function test_update_incorrect_name(): void
	{
		self::markTestSkipped("need to add validation rules to TaxUpdateInput");
		Tax::factory()->create();
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => "1",
			"name" => "",
			"percent" => 10,
		]);
		$this->assertContains(
			"The value must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_update_not_unique_name(): void
	{
		self::markTestSkipped("need to add validation rules to TaxUpdateInput");
		Tax::factory()->create();
		$this->postGraphqlJsonWithVariables($this->query, [
			"id" => "1",
			"name" => "test name",
			"percent" => 10,
		]);
		Tax::factory()->create();
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => "2",
			"name" => "test name",
			"percent" => 10,
		]);
		$this->assertContains(
			"The value must be unique",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
