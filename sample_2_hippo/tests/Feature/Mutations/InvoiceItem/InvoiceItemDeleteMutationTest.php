<?php
namespace Tests\Feature\Mutations\InvoiceItem;

use App\Models\InvoiceItem;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceItemDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_invoice_item_can_be_deleted()
	{
		$invoiceItem = InvoiceItem::factory()->create();
		$query = '
      mutation InvoiceItemDelete($input: invoiceItemDeleteInput!) {
        invoiceItemDelete(input: $input) {
          data {
						id
					}
				}
			}
    ';
		$input = [
			"input" => [
				"invoiceItem" => $invoiceItem->id,
			],
		];

		$this->postGraphqlJsonWithVariables($query, $input)
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"invoiceItemDelete" => [
						"data" => [],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"invoiceItemDelete" => [
						"data" => [],
					],
				],
			]);
	}

	public function test_invoice_item_delete_fails_with_null_id()
	{
		$query = '
      mutation InvoiceItemDelete($input: invoiceItemDeleteInput!) {
        invoiceItemDelete(input: $input) {
          data {
						id
					}
				}
			}
    ';
		$input = [
			"input" => [
				"invoiceItem" => null,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"The input.invoice item field is required.",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_invoice_item_delete_fails_with_invalid_id()
	{
		$query = '
      mutation InvoiceItemDelete($input: invoiceItemDeleteInput!) {
        invoiceItemDelete(input: $input) {
          data {
						id
					}
				}
			}
    ';
		$input = [
			"input" => [
				"invoiceItem" => -99999,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $input);
		$this->assertContains(
			"Please select a valid invoice",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}
}
