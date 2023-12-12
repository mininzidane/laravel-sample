<?php

namespace Tests\Feature\Mutations\Invoice;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Location;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\State;
use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_can_create_invoice()
	{
		$patient = Patient::factory()->create();

		$state = State::factory()->create([
			"name" => "Alabama",
			"code" => "AL",
		]);

		$owner = Owner::factory()->create([
			"state" => $state->id,
		]);

		$location = Location::factory()->create();

		$query =
			'
            mutation {
                invoiceCreate (input: {id: "", comment: "", printComment: false, rounding: 0, isTaxable: false, total: 0, patient: ' .
			$patient->id .
			", owner: " .
			$owner->id .
			", location: " .
			$location->id .
			'}) {
                    data {
                        id
                        comment
                        printComment
                        rounding
                        isTaxable
                        isActive
                        total
                        amountDue
                        totalPayments
                        createdAt
                        completedAt
                        totalPayments
                        invoiceStatus {
                            id
                            name
                        }
                        location {
                            id
                            name
                            streetAddress
                            address1
                            city
                            subregion {
                                name
                                code
                            }
                            tz {
                                name
                                offset
                                php_supported
                            }
                            organization {
                                currencySymbol
                            }
                            zip
                            imageName
                            imageUrl
                        }
                        patient {
                            id
                            name
                        }
                        owner {
                            id
                            firstName
                            lastName
                            fullName
                            address1
                            address2
                            city
                            subregion{
                                name
                                code
                            }
                            zip
                            phone
                            email
                        }
                        user {
                            id
                        }
                        invoiceItems {
                            id
                            line
                            quantity
                            name
                            number
                            price
                            discountPercent
                            discountAmount
                            total
                            serialNumber
                            administeredDate
                            description
                            allowAltDescription
                            costPrice
                            volumePrice
                            markupPercentage
                            unitPrice
                            minimumSaleAmount
                            dispensingFee
                            isVaccine
                            isPrescription
                            isSerialized
                            isControlledSubstance
                            isEuthanasia
                            isReproductive
                            hideFromRegister
                            requiresProvider
                            isInWellnessPlan
                            vcpItemId
                            drugIdentifier
                            belongsToKitId
                            isSingleLineKit
                            credit {
                                number
                            }
                            provider {
                                id
                                firstName
                                lastName
                            }
                            chart {
                                id
                                chartType
                                createdAt
                                updatedAt
                            }
                            item {
                                remaining
                                itemType {
                                    name
                                }
                                category {
                                    name
                                }
                            }
                            invoiceItemTaxes {
                                id
                                name
                                percent
                                amount
                                tax {
                                    id
                                }
                            }
                            inventoryTransactions {
                                id
                                inventory {
                                    id
                                    expirationDate
                                }
                            }
                        }
                        invoicePayments {
                            id
                            amountApplied
                            payment {
                                id
                                amount
                                receivedAt
                                paymentMethod {
                                    id
                                    name
                                }
                            }
                        }
                    }
                }
            }
            ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"invoiceCreate" => [
					"data" => [
						"*" => [
							"id",
							"comment",
							"printComment",
							"rounding",
							"isTaxable",
							"isActive",
							"total",
							"amountDue",
							"totalPayments",
							"createdAt",
							"completedAt",
							"totalPayments",
							"invoiceStatus" => ["id", "name"],
							"location" => [
								"id",
								"name",
								"streetAddress",
								"address1",
								"city",
								"subregion" => ["name", "code"],
								"tz" => ["name", "offset", "php_supported"],
								"organization" => ["currencySymbol"],
								"zip",
								"imageName",
								"imageUrl",
							],
							"patient" => ["id", "name"],
							"owner" => [
								"id",
								"firstName",
								"lastName",
								"fullName",
								"address1",
								"address2",
								"city",
								"subregion" => ["name", "code"],
								"zip",
								"phone",
								"email",
							],
							"user" => ["id"],
							"invoiceItems" => [],
							"invoicePayments" => [],
						],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"invoiceCreate" => [
					"data" => [
						[
							"id" => "1",
							"comment" => "",
							"printComment" => false,
							"rounding" => 0,
							"isTaxable" => (bool) $owner->taxable,
							"isActive" => false,
							"total" => 0,
							"amountDue" => 0,
							"totalPayments" => 0,
							"createdAt" => "$this->carbonTestTime",
							"completedAt" => null,
							"invoiceStatus" => ["id" => "1", "name" => "Open"],
							"location" => [
								"id" => "$location->id",
								"name" => "$location->name",
								"streetAddress" => "$location->address1",
								"address1" => "$location->address1",
								"city" => "$location->city",
								"subregion" => [
									"name" => "{$location->subregion->name}",
									"code" => "{$location->subregion->code}",
								],
								"tz" => [
									"name" => "{$location->tz->value}",
									"offset" => "{$location->tz->offset}",
									"php_supported" => "{$location->tz->php_supported}",
								],
								"organization" => ["currencySymbol" => '$'],
								"zip" => "$location->zip",
								"imageName" => $location->image_name,
								"imageUrl" => $location->imageUrl,
							],
							"patient" => [
								"id" => "$patient->id",
								"name" => "$patient->name",
							],
							"owner" => [
								"id" => "$owner->id",
								"firstName" => "$owner->first_name",
								"lastName" => "$owner->last_name",
								"fullName" => "$owner->full_name",
								"address1" => "$owner->address1",
								"address2" => "$owner->address2",
								"city" => "$owner->city",
								"subregion" => [
									"name" => "{$owner->subregion->name}",
									"code" => "{$owner->subregion->code}",
								],
								"zip" => "$owner->zip",
								"phone" => "$owner->phone",
								"email" => "$owner->email",
							],
							"user" => ["id" => "1"],
							"invoiceItems" => [],
							"invoicePayments" => [],
						],
					],
				],
			],
		]);
	}

	public function test_can_void_invoice()
	{
		$invoice = Invoice::factory()->create();

		$query =
			'            
        mutation {
          invoiceVoid (input: {id: "' .
			$invoice->id .
			'"}) {
            data {
              id
            }
          }
        }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"invoiceVoid" => [
					"data" => [],
				],
			],
		]);

		$response->assertExactJson([
			"data" => ["invoiceVoid" => ["data" => []]],
		]);

		$this->assertSoftDeleted("invoices", ["id" => $invoice->id]);
	}

	public function test_can_complete_invoice()
	{
		$invoice = Invoice::factory()->create();

		$query =
			'            
        mutation {
          invoiceComplete (input: {id: "' .
			$invoice->id .
			'"}) {
            data {
              id
            }
          }
        }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"invoiceComplete" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"invoiceComplete" => ["data" => [["id" => "$invoice->id"]]],
			],
		]);

		$this->assertDatabaseHas("invoices", [
			"id" => $invoice->id,
			"completed_at" => $this->carbonTestTime,
			"original_completed_at" => $this->carbonTestTime,
		]);
	}

	public function test_invoice_item_can_be_updated()
	{
		$invoice = Invoice::factory()->create([
			"status_id" => 1,
		]);
		$item = Item::factory()->create([
			"type_id" => 3,
		]);

		$this->add_item_to_invoice($item->id, $invoice->id);

		$query = '
              mutation InvoiceItemUpdate($input: invoiceItemUpdateInput!) {
                invoiceItemUpdate(input: $input) {
                  data {
                    id
                  }
                }
              }
        ';

		//assume there is only one id and the id is 1
		$variables = [
			"input" => [
				"id" => 1,
				"chart" => 0,
				"chartType" => "",
				"description" => "",
				"quantity" => "55",
				"price" => 0,
				"administeredDate" => "2022-09-29",
				"discountPercent" => 0,
				"discountAmount" => 0,
				"unitPrice" => 0,
				"dispensingFee" => 0,
				"hideFromRegister" => false,
				"serialNumber" => null,
				"allowExcessiveQuantity" => false,
				"provider" => 0,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"invoiceItemUpdate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"invoiceItemUpdate" => ["data" => [["id" => "$invoice->id"]]],
			],
		]);

		$this->assertDatabaseHas("invoice_items", [
			"id" => 1,
			"quantity" => 55,
		]);
	}

	public function test_can_add_item_to_invoice()
	{
		$invoice = Invoice::factory()->create([
			"status_id" => 1,
		]);
		$item = Item::factory()->create([
			"type_id" => 3,
		]);

		$query = '
              mutation InvoiceItemCreate($input: invoiceItemCreateInput!) {
                invoiceItemCreate(input: $input) {
                  data {
                    id
                  }
                }
              }
            ';

		$variables = [
			"input" => [
				"chart" => 0,
				"chartType" => "",
				"quantity" => 1,
				"administeredDate" => "September 29 2022",
				"item" => "1",
				"invoice" => "$invoice->id",
				"provider" => 0,
				"allowExcessiveQuantity" => false,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"invoiceItemCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"invoiceItemCreate" => ["data" => [["id" => "$invoice->id"]]],
			],
		]);
	}

	public function test_can_save_invoice_save_details()
	{
		$invoice = Invoice::factory()->create();
		$invoice2 = Invoice::factory()->create();

		$query =
			'            
            mutation {
            invoiceSaveDetails (
                input:
                {
                    id: "' .
			$invoice2->id .
			'",
                    comment: "I am a little comment",
                    isTaxable: false,
                    isEstimate: false,
                    printComment: true
                }) {
                data {
                    id
                }
            }
        }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"invoiceSaveDetails" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"invoiceSaveDetails" => ["data" => [["id" => "$invoice2->id"]]],
			],
		]);

		$this->assertDatabaseHas("invoices", [
			"id" => $invoice2->id,
			"print_comment" => true,
		]);
	}

	public function test_can_reopen_invoice()
	{
		$invoice = Invoice::factory()->create();
		$this->create_closed_invoice($invoice->id);

		$query =
			'            
            mutation {
            invoiceReopen (
                input:
                {
                    invoiceId: "' .
			$invoice->id .
			'"
                }) {
                data {
                    id
                    comment
                    active
                    isTaxable
                }
            }
        }';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"invoiceReopen" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"invoiceReopen" => [
					"data" => [
						[
							"id" => "$invoice->id",
							"comment" => "$invoice->comment",
							"active" => true,
							"isTaxable" => (bool) $invoice->is_taxable,
						],
					],
				],
			],
		]);

		//here we are making sure the invoice status is open
		$this->assertDatabaseHas("invoices", [
			"id" => $invoice->id,
			"status_id" => 1,
			"original_completed_at" => $this->carbonTestTime,
		]);
	}

	public function test_can_close_reopen_close_invoice()
	{
		$invoice = Invoice::factory()->create();
		$this->create_closed_invoice($invoice->id);

		$query =
			'            
            mutation {
            invoiceReopen (
                input:
                {
                    invoiceId: "' .
			$invoice->id .
			'"
                }) {
                data {
                    id
                    comment
                    active
                    isTaxable
                }
            }
        }';

		$this->postGraphqlJson($query)->assertExactJson([
			"data" => [
				"invoiceReopen" => [
					"data" => [
						[
							"id" => "$invoice->id",
							"comment" => "$invoice->comment",
							"active" => true,
							"isTaxable" => (bool) $invoice->is_taxable,
						],
					],
				],
			],
		]);

		//here we are making sure the invoice status is open
		$this->assertDatabaseHas("invoices", [
			"id" => $invoice->id,
			"status_id" => 1,
			"original_completed_at" => $this->carbonTestTime,
		]);

		$this->create_closed_invoice($invoice->id);
		$this->assertDatabaseHas("invoices", [
			"id" => $invoice->id,
			"status_id" => 2,
			"original_completed_at" => $this->carbonTestTime,
		]);
	}

	public function test_can_set_active_invoice()
	{
		$invoice = Invoice::factory()->create([
			"status_id" => 1,
		]);

		$query =
			'mutation {
          invoiceSetActive (input: {invoiceId: "' .
			$invoice->id .
			'"}) {
            data {
              id
              comment
              active
              isTaxable
            }
          }
        }';

		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200);
		$response->assertJsonStructure([
			"data" => [
				"invoiceSetActive" => [
					"data" => [
						"*" => ["id", "comment", "active", "isTaxable"],
					],
				],
			],
		]);

		//here we are making sure the invoice status is open
		$this->assertDatabaseHas("invoices", [
			"id" => $invoice->id,
			"status_id" => 1,
		]);
	}
}
