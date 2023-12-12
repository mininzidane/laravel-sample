<?php

namespace Tests\Feature\Mutations\Markings;

use App\Models\Markings;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class MarkingsDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	const DELETE_QUERY = '
            mutation MarkingsDeleteMutation($id: Int!) {
                markingsDelete(id: $id) {
                    data {
                        id
                    }
                }
            }
        ';

	/**
	 * Test if Markings can be deleted successfully with expected Markings ID.
	 * @return void
	 */
	public function test_markings_can_be_deleted()
	{
		$this->postGraphqlJsonWithVariables(self::DELETE_QUERY, [
			"id" => Markings::factory()->create()->id,
		])
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"markingsDelete" => [
						"data" => [
							"*" => ["id"],
						],
					],
				],
			]);
	}

	/**
	 * Test if Markings fail to be deleted when no ID key is present.
	 * @return void
	 */
	public function test_markings_cannot_be_deleted_without_id_key()
	{
		$response = $this->postGraphqlJsonWithVariables(
			self::DELETE_QUERY,
			[],
		)->assertStatus(200);

		$this->assertContains(
			'Variable "$id" of required type "Int!" was not provided.',
			$response->json("*.*.message"),
		);
	}

	/**
	 * Test if Markings fail to be deleted when the ID value is null.
	 * @return void
	 */
	public function test_markings_cannot_be_deleted_with_null_id()
	{
		$response = $this->postGraphqlJsonWithVariables(self::DELETE_QUERY, [
			"id" => null,
		])->assertStatus(200);

		$this->assertContains(
			'Variable "$id" got invalid value null; Expected non-nullable type Int! not to be null.',
			$response->json("*.*.message"),
		);
	}

	/**
	 * Test if Markings fail to be deleted when the ID value is an Integer with reference to a Markings that does not exist.
	 * @return void
	 */
	public function test_markings_cannot_be_deleted_with_nonexistent_id()
	{
		$response = $this->postGraphqlJsonWithVariables(
			self::DELETE_QUERY,
			// ID referenced here is the maximum integer value.
			["id" => 2147483647],
		)->assertStatus(200);

		$this->assertContains(
			"No query results for model [App\Models\Markings] 2147483647",
			$response->json("errors.*.debugMessage"),
		);
	}
}
