<?php

namespace Tests\Feature\Mutations\Supplier;

use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class SupplierCreateMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	protected const QUERY = 'mutation SupplierCreate ($input: supplierCreateInput!) {
                                supplierCreate (input: $input) {
                                    data {
                                        id,
                                        companyName
                                    }
                                }
                            }';
	private array $variables;

	public function setUp(): void
	{
		parent::setUp();

		$this->setUpVariables();
	}

	public function creationDataProvider(): array
	{
		return [
			"Minimal data" => [
				"input" => [
					"accountNumber" => null,
					"contactName" => null,
					"emailAddress" => null,
					"phoneNumber" => null,
					"address1" => null,
					"address2" => null,
					"city" => null,
					"zipCode" => null,
					"state" => null,
				],
			],
			"Full data" => ["input" => []],
		];
	}

	/**
	 * @dataProvider creationDataProvider
	 */

	public function test_supplier_can_be_created(array $input)
	{
		$this->variables["input"] = array_merge(
			$this->variables["input"],
			$input,
		);

		$response = $this->postGraphqlJsonWithVariables(
			self::QUERY,
			$this->variables,
		);
		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"supplierCreate" => [
						"data" => [
							"*" => ["id", "companyName"],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"supplierCreate" => [
						"data" => [
							[
								"id" => "1",
								"companyName" => "Hip Place",
							],
						],
					],
				],
			]);
	}

	public function validationFailedDataProvider(): array
	{
		return [
			"Empty Company name" => [
				"variablesInput" => ["companyName" => null],
				"errorMessage" => "The Supplier name is required",
			],
			"Too long Company name" => [
				"variablesInput" => ["companyName" => str_repeat("A", 256)],
				"errorMessage" =>
					"The input.company name may not be greater than 255 characters.",
			],
		];
	}

	/**
	 * @dataProvider validationFailedDataProvider
	 */

	public function test_supplier_cant_be_created_with_failed_validation(
		array $variablesInput,
		string $errorMessage
	) {
		$this->variables["input"] = array_merge(
			$this->variables["input"],
			$variablesInput,
		);

		$response = $this->postGraphqlJsonWithVariables(
			self::QUERY,
			$this->variables,
		);

		$response->assertStatus(200);
		$this->assertContains(
			$errorMessage,
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	private function setUpVariables(): void
	{
		$this->variables = [
			"input" => [
				"companyName" => "Hip Place",
				"accountNumber" => 1234,
				"contactName" => "The Hip",
				"emailAddress" => "hippo@hippo.com",
				"phoneNumber" => "555-867-5309",
				"address1" => "123 address way",
				"address2" => "second address",
				"city" => "boom town",
				"zipCode" => "40383",
				"state" => 3697,
			],
		];
	}
}
