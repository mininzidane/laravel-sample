<?php

namespace Tests\Feature\Mutations\Supplier;

use App\Models\Supplier;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class SupplierUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected const QUERY = 'mutation SupplierUpdate ($id: Int!, $input: supplierUpdateInput!) {
                        supplierUpdate (id: $id, input: $input) {
                            data {
                                id,
                                companyName
                            }
                        }
                    }';

	private Supplier $supplier;

	private array $variables;

	public function setUp(): void
	{
		parent::setUp();

		$this->setUpModels();
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
	public function test_supplier_can_be_updated(array $input)
	{
		$this->variables["input"] = array_merge(
			$this->variables["input"],
			$input,
		);

		$response = $this->postGraphqlJsonWithVariables(
			self::QUERY,
			$this->variables,
		);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"supplierUpdate" => [
					"data" => [
						"*" => ["id", "companyName"],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"supplierUpdate" => [
					"data" => [
						[
							"id" => (string) $this->supplier->id,
							"companyName" =>
								$this->variables["input"]["companyName"],
						],
					],
				],
			],
		]);
	}

	public function validationFailedDataProvider(): array
	{
		return [
			"Empty Item name" => [
				"variablesInput" => ["input" => ["companyName" => null]],
				"errorMessage" => "Supplier name is required",
			],
		];
	}

	/**
	 * @dataProvider validationFailedDataProvider
	 */
	public function test_supplier_cant_be_updated_with_failed_validation(
		array $variablesInput,
		string $errorMessage
	) {
		$this->variables = array_merge($this->variables, $variablesInput);

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

	public function WrongIdDataProvider(): array
	{
		return [
			"No id" => [
				"id" => null,
				"errorMessage" =>
					'Variable "$id" got invalid value null; Expected non-nullable type Int! not to be null.',
			],
			"Wrong id" => [
				"id" => 1234,
				"errorMessage" => "Internal server error",
			],
		];
	}

	/**
	 * @dataProvider WrongIdDataProvider
	 */
	public function test_supplier_cant_be_updated_wrong_id(
		?int $id,
		string $errorMessage
	) {
		$this->variables["id"] = $id;

		$response = $this->postGraphqlJsonWithVariables(
			self::QUERY,
			$this->variables,
		);

		$response->assertStatus(200);
		$this->assertContains(
			$errorMessage,
			$response->json("errors.*.message"),
		);
	}

	public function test_deleted_supplier_cant_be_updated()
	{
		/** @var Supplier $deletedSupplier */
		$deletedSupplier = Supplier::factory()->create(["deleted_at" => now()]);
		$this->variables["id"] = $deletedSupplier->id;

		$response = $this->postGraphqlJsonWithVariables(
			self::QUERY,
			$this->variables,
		);

		$response->assertStatus(200);
		$this->assertContains(
			"Internal server error",
			$response->json("errors.*.message"),
		);
	}

	private function setUpModels(): void
	{
		$this->supplier = Supplier::factory()->create();
	}

	private function setUpVariables(): void
	{
		$this->variables = [
			"id" => $this->supplier->id,
			"input" => [
				"companyName" => "Hippo Place",
				"accountNumber" => 1234,
				"contactName" => "nick",
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
