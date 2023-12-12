<?php

namespace Tests\Feature\Mutations\Supplier;

use App\Models\Supplier;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class SupplierDeleteMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected const QUERY = 'mutation SupplierDeleteMutation($id: String){
                    supplierDelete(id: $id) 
                    {
                        data {
                            id
                        }
                    }
                }';

	public function test_supplier_can_be_deleted()
	{
		/**
		 * To check that not only first supplier deletes correctly
		 * we are creating 10 suppliers and delete a random one
		 * @var Supplier[] $suppliersList
		 */
		$suppliersList = [];

		for ($i = 0; $i < 10; $i++) {
			$suppliersList[] = Supplier::factory()->create();
		}

		$supplier = $suppliersList[array_rand($suppliersList)];
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => $supplier->id,
		]);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"supplierDelete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"supplierDelete" => [
					"data" => [
						[
							"id" => (string) Supplier::query()->first()->id, //SupplierDeleteMutation returns id of the first not deleted Supplier
						],
					],
				],
			],
		]);
	}

	public function test_supplier_cant_be_deleted_twice()
	{
		/** @var Supplier $supplier */
		$supplier = Supplier::factory()->create();

		$this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => $supplier->id,
		]);
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => $supplier->id,
		]);

		$response->assertStatus(200);

		$this->assertContains(
			"Internal server error",
			$response->json("errors.*.message"),
		);
	}

	public function test_supplier_cant_be_deleted_wrong_id()
	{
		$response = $this->postGraphqlJsonWithVariables(self::QUERY, [
			"id" => 1234,
		]);

		$response->assertStatus(200);

		$this->assertContains(
			"Internal server error",
			$response->json("errors.*.message"),
		);
	}
}
