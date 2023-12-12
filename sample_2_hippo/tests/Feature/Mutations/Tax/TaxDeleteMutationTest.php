<?php

declare(strict_types=1);

namespace Tests\Feature\Mutations\Tax;

use App\Models\Tax;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class TaxDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected string $query = '
		mutation TaxDeleteMutation($id: String) {
			taxDelete(
				id: $id
				invoiceItemTaxes: ""
				items: ""
				name: ""
				percent: ""
				relationshipNumber: ""
				subdomain: ""
			) {
				data {
					id,
					percent
				}
			}
		}
	';

	public function test_can_delete_tax(): void
	{
		/** @var Tax $tax */
		$tax = Tax::factory()->create();
		$this->postGraphqlJsonWithVariables($this->query, [
			"id" => "1",
		]);
		$tax->refresh();
		$this->assertDatabaseHas("taxes", [
			"id" => 1,
		]);
		self::assertNotNull($tax->deleted_at);
	}

	public function test_delete_incorrect_id(): void
	{
		$response = $this->postGraphqlJsonWithVariables($this->query, [
			"id" => "1",
		]);
		self::assertIsArray($response->json("errors"));
	}
}
