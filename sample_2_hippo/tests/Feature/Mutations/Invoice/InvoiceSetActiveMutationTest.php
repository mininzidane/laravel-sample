<?php

namespace Tests\Feature\Mutations\Invoice;

use App\Models\Invoice;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceSetActiveMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	private string $query = '
            mutation InvoiceSetActiveMutation($input: invoiceSetActiveInput!){
                invoiceSetActive(input: $input){
                     data {
                      id
                      comment
                      active
                      isTaxable
                    }
                }
            }
    ';

	private array $roles = [
		"auth_signatory",
		"cashier",
		"financial_reporting",
		"forms_certificates",
		"inventory_reporting",
		"office_manager",
		"patient_records",
		"scheduling",
		"super_user",
		"trans_manager",
		"end_of_period_reporting",
	];

	public function testEveryRoleCanUseMutation()
	{
		foreach ($this->roles as $role) {
			$this->changeUserRole($role);

			/** @var Invoice $invoice */
			$invoice = Invoice::factory()->create(["active" => false]);

			$this->assertTrue($invoice->active === false);

			$variables = [
				"input" => [
					"invoiceId" => $invoice->id,
				],
			];

			$response = $this->postGraphqlJsonWithVariables(
				$this->query,
				$variables,
			);

			$response->assertStatus(200);
			$response
				->assertJsonStructure([
					"data" => [
						"invoiceSetActive" => [
							"data" => [
								"*" => ["id", "comment", "active", "isTaxable"],
							],
						],
					],
				])
				->assertJsonFragment([
					"id" => "{$invoice->id}",
					"active" => true,
				]);
		}
	}

	/**
	 * Changing role for current user isn't a simple action (especially since $this->user has private access level)
	 * Thus, it's easier to create a new user than change a role for the current one
	 * Also we do truncate tblUser so it should not cause any problems
	 */
	private function changeUserRole(string $role): void
	{
		$subdomain = config("testing.subdomain");
		$connection = config("testing.default");
		$database = config("testing.connections.$connection.database");
		$this->createUserWithAccessLevel($database, $role);
		$this->createAccessToken();
		$this->setUpHeaders($subdomain);
	}
}
