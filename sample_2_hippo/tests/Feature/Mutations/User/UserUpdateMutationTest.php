<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\User;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class UserUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	private User $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create();
	}

	public function test_user_can_be_updated(): void
	{
		$response = $this->updateUser($this->user->id);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"userUpdate" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"userUpdate" => [
						"data" => [
							[
								"id" => (string) $this->user->id,
							],
						],
					],
				],
			]);
		$id = $response->json("data.userUpdate.data.0.id");
		$this->assertDatabaseHas("tblUsers", [
			"id" => $id,
			"username" => "Bobby@bobmail.com",
			"last_name" => "bobber",
			"first_name" => "bob",
			"degree" => "BVMS",
			"active" => 1,
			"specialty" => "bobbs",
			"license" => "123456",
			"ein" => "46579",
			"dea" => "1",
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
		$this->assertDatabaseHas("tblUserAccessLevels", [
			"user_id" => $id,
		]);
	}

	public function test_user_incorrect_id(): void
	{
		$response = $this->updateUser(9999);
		$this->assertContains(
			"No query results for model [App\\Models\\User] 9999",
			$response->json("*.*.errorMessage"),
		);
	}

	public function test_can_reset_clientEd_username_password()
	{
		$response = $this->updateUser($this->user->id, [
			"clientedUsername" => null,
			"clientedPassword" => null,
		]);
		$id = $response->json("data.userUpdate.data.0.id");
		$this->assertDatabaseHas("tblUsers", [
			"id" => $id,
			"cliented_username" => null,
			"cliented_password" => null,
		]);
	}

	public function test_username_required(): void
	{
		$response = $this->updateUser($this->user->id, ["username" => ""]);
		$this->assertContains(
			"Username is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_lastname_required(): void
	{
		$response = $this->updateUser($this->user->id, ["lastName" => ""]);
		$this->assertContains(
			"Last name is required",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	private function updateUser(int $userId, array $input = []): TestResponse
	{
		$query = 'mutation UserUpdateMutation($id: Int, $input: userUpdateInput) {
              userUpdate(id: $id, input: $input) {
                data { id }
              }
            }';
		$input = array_merge(
			[
				"username" => "Bobby@bobmail.com",
				"lastName" => "bobber",
				"firstName" => "bob",
				"degree" => "BVMS",
				"active" => true,
				"specialty" => "bobbs",
				"roles" => 1,
				"locations" => 1,
				"license" => "123456",
				"ein" => "46579",
				"dea" => "1",
				"clientedUsername" => "test@hippomanager.com",
				"clientedPassword" => "test_test",
			],
			$input,
		);

		return $this->postGraphqlJsonWithVariables($query, [
			"id" => $userId,
			"input" => $input,
		]);
	}
}
