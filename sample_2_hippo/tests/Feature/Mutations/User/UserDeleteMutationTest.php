<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\User;

use App\Models\User;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class UserDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	private User $user;

	private const QUERY = 'mutation UserDeleteMutation($id: Int) {
          userDelete(id: $id) {
            data { id }
          }
        }';

	public function setUp(): void
	{
		parent::setUp();
		$this->user = User::factory()->create();
	}

	public function test_user_can_be_removed(): void
	{
		$this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => $this->user->id,
		]);
		$this->assertDatabaseHas("tblUsers", [
			"id" => $this->user->id,
			"removed" => 1,
		]);
	}

	public function test_user_required_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => "",
		]);
		$this->assertContains(
			"An ID is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_user_incorrect_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => 9999,
		]);
		$this->assertContains(
			"No query results for model [App\\Models\\User] 9999",
			$response->json("*.*.errorMessage"),
		);
	}
}
