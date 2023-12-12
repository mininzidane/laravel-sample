<?php

namespace Tests\Feature\Mutations\Invoice;

use App\Models\Location;
use App\Models\Owner;
use App\Models\Patient;
use App\Models\State;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceCreatePlaceholderTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	private string $query = '
            mutation InvoiceCreatePlaceholderMutation($locationId: Int) {
                invoiceCreatePlaceholder (input: 
                    {
                        id: "", 
                        comment: "", 
                        printComment: false, 
                        rounding: 0, 
                        isTaxable: false, 
                        total: 0, 
                        patient: %d, 
                        owner: %d, 
                        location: $locationId
                    }
                ) {
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

	public function test_can_create_invoice()
	{
		/** @var Patient $patient */
		$patient = Patient::factory()->create();

		/** @var State $state */
		$state = State::factory()->create([
			"name" => "Alabama",
			"code" => "AL",
		]);

		/** @var Owner $owner */
		$owner = Owner::factory()->create([
			"state" => $state->id,
		]);

		/** @var Location $location */
		$location = Location::factory()->create();

		$query = sprintf($this->query, $patient->id, $owner->id);
		$variables = ["locationId" => $location->id];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		$response->assertJsonStructure([
			"data" => [
				"invoiceCreatePlaceholder" => [
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
				"invoiceCreatePlaceholder" => [
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
}
