<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\User;

use App\Models\Organization;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class UserCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_user_can_be_created(): void
	{
		$response = $this->createUser();
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"userCreate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"userCreate" => [
						"data" => [
							[
								"id" => "2",
							],
						],
					],
				],
			]);
		$id = $response->json("data.userCreate.data.0.id");
		$this->assertDatabaseHas("tblUsers", [
			"id" => $id,
		]);
		$this->assertDatabaseHas("tblUserLocations", [
			"user_id" => $id,
			"location_id" => 1,
		]);
		$this->assertDatabaseHas("model_has_roles", [
			"model_id" => $id,
			"model_type" => "App\Models\User",
			"role_id" => 1,
		]);
	}

	public function test_username_required(): void
	{
		$response = $this->createUser(["username" => ""]);
		$this->assertContains(
			"Username is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_username_max_exceeded(): void
	{
		$username = str_repeat("a", 193);
		$response = $this->createUser(["username" => $username]);
		$this->assertContains(
			"Username cannot be longer than 191 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_username_not_unique(): void
	{
		$username = "test@test.test";
		$this->createUser(["username" => $username]);
		$response = $this->createUser(["username" => $username]);
		$this->assertContains(
			"An account exists already with this username. Contact support if you need to restore a previously deleted user.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_username_not_email(): void
	{
		$response = $this->createUser(["username" => "test"]);
		$this->assertContains(
			"The input.username must be a valid email address.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_lastname_required(): void
	{
		$response = $this->createUser(["lastName" => ""]);
		$this->assertContains(
			"Last name is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_lastname_max_exceeded(): void
	{
		$lastName = str_repeat("a", 52);
		$response = $this->createUser(["lastName" => $lastName]);
		$this->assertContains(
			"Last name cannot be longer than 50 characters",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_degree_does_not_exist(): void
	{
		$response = $this->createUser(["degree" => "test"]);
		$this->assertContains(
			"The specified degree does not exists",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_required(): void
	{
		$response = $this->createUser(["locations" => ""]);
		$this->assertContains(
			"A location is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_location_not_exists(): void
	{
		$response = $this->createUser(["locations" => 999]);
		$this->assertContains(
			"A specified location does not exist.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_role_required(): void
	{
		$response = $this->createUser(["roles" => ""]);
		$this->assertContains(
			"A user role is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_role_not_exists(): void
	{
		$response = $this->createUser(["roles" => 999]);
		$this->assertContains(
			"A specified role does not exist",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	private function createUser(array $input = []): TestResponse
	{
		/** @var Organization $organization */
		$organization = Organization::factory()->create();
		$query = 'mutation UserCreateMutation($input: userCreateInput) {
              userCreate (input: $input) {
                data { id }
              }
            }';
		$input = array_merge(
			[
				"username" => "Bobby@bobmail.com",
				"firstName" => "bob",
				"lastName" => "bobber",
				"degree" => "BVMS",
				"active" => true,
				"specialty" => "bobbs",
				"roles" => 1,
				"locations" => 1,
				"organization" => $organization->id,
				"license" => "123456",
				"ein" => "46579",
				"dea" => "1",
			],
			$input,
		);

		return $this->postGraphqlJsonWithVariables($query, [
			"input" => $input,
		]);
	}
}
