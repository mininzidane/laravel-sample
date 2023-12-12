<?php

namespace Tests\Helpers;

use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;

trait MutationTestHelpers
{
	/**
	 * @param int $itemTypeId stocking id
	 */
	public function create_item(int $itemTypeId = 2): TestResponse
	{
		$this->tax_create();
		$this->create_item_category();
		$this->create_supplier();
		$query = 'mutation ItemCreate($unitPriceDisabled: Boolean!, $input: ItemCreateInput!) {
                itemCreate(unitPriceDisabled: $unitPriceDisabled, input: $input) {
                    data {
                        id
                    }
                }
            }';
		$variables = [
			"input" => [
				"name" => "bob says",
				"number" => "3333333",
				"itemTypeId" => $itemTypeId,
				"chartOfAccount" => "1",
				"categoryId" => "1",
				"description" => "asdfasdf",
				"allowAltDescription" => true,
				"isVaccine" => true,
				"isPrescription" => true,
				"isSerialized" => true,
				"isControlledSubstance" => true,
				"isInWellnessPlan" => true,
				"isEuthanasia" => true,
				"isReproductive" => true,
				"requiresProvider" => false,
				"hideFromRegister" => true,
				"costPrice" => 3,
				"minimumSaleAmount" => 3,
				"markupPercentage" => 33,
				"dispensingFee" => 3,
				"unitPrice" => 3.99,
				"isNonTaxable" => false,
				"applyToRemainder" => true,
				"reminderIntervalId" => "1",
				"reminderReplaces" => [],
				"minimumOnHand" => 1,
				"maximumOnHand" => 45,
				"nextTagNumber" => null,
				"drugIdentifier" => "",
				"isSingleLineKit" => false,
				"itemSpeciesRestrictions" => [],
				"itemLocations" => [
					[
						"id" => 1,
					],
				],
				"itemTaxes" => [
					[
						"id" => 1,
					],
				],
				"itemVolumePricing" => [
					[
						"id" => 0,
						"quantity" => 1,
						"unitPrice" => 23,
					],
					[
						"id" => 0,
						"quantity" => 3,
						"unitPrice" => 44,
					],
				],
				"itemKitItems" => [],
				"manufacturerId" => "1",
			],
			"unitPriceDisabled" => false,
		];

		return $this->postGraphqlJsonWithVariables($query, $variables);
	}

	public function create_item_category()
	{
		$query = 'mutation {
            itemCategoryCreate (input: {
                id: "",
                name: "Test Category"
            }) {
                data {
                    id
                }
            }
        }
      ';

		$response = $this->postGraphqlJson($query);
	}

	public function create_supplier()
	{
		$query = 'mutation SupplierCreate ($input: supplierCreateInput!) {
                    supplierCreate (input: $input) {
                        data {
                            id
                        }
                    }
                }';

		$variables = [
			"input" => [
				"companyName" => "nicks place",
				"accountNumber" => "234234",
				"contactName" => "Nick",
				"emailAddress" => "",
				"phoneNumber" => "",
				"address1" => "",
				"address2" => "",
				"city" => "",
				"zipCode" => null,
				"state" => 3697,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);
	}

	public function create_invoice()
	{
		//Fix created at time issues
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = '
            mutation {
                invoiceCreate (input: {id: "", comment: "", printComment: false, rounding: 0, isTaxable: false, total: 0, patient: 2, owner: 2, location: 1}) {
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
	}

	public function create_closed_invoice($invoiceId)
	{
		$query =
			'            
        mutation {
          invoiceComplete (input: {id: "' .
			$invoiceId .
			'"}) {
            data {
              id
            }
          }
        }';

		$response = $this->postGraphqlJson($query);
	}

	public function add_item_to_invoice($itemId = 1, $invoiceId = 1)
	{
		//TODO replace with facotry

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
				"item" => "$itemId",
				"invoice" => "$invoiceId",
				"provider" => 1,
				"allowExcessiveQuantity" => false,
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);
	}

	public function create_receiving()
	{
		$query = '
            mutation {
                receivingCreate (input: {id: "", comment: "New Receiving", location: 1}) {
                    data {
                        id
                        receivedAt
                        createdAt
                        updatedAt
                        comment
                        location {
                            id
                            name
                        }
                        supplier {
                            id
                            companyName
                        }
                        user {
                            id
                            firstName
                            lastName
                        }
                        receivingItems {
                            id
                            line
                            quantity
                            comment
                            costPrice
                            discountPercentage
                            item {
                                name
                                description
                                unitPrice
                                number
                                costPrice
                                remaining
                            }
                            inventory {
                                id
                                lotNumber
                                serialNumber
                                expirationDate
                            }
                        }
                        receivingStatus {
                            name
                        }
                        items {
                            id
                            name
                        }
                    }
                }
            }
            ';

		$response = $this->postGraphqlJson($query);
	}

	public function update_by_create_receiving(): TestResponse
	{
		$query = '
            mutation {
                receivingItemCreate(input: {quantity: 55, item: 1, receiving: 1}) {
                    data {
            
                        id
                        line
                        quantity
                        comment
                        costPrice
                        discountPercentage
                        item {
                            name
                            description
                            unitPrice
                            number
                            costPrice
                        }
                        inventory {
                            id
                            lotNumber
                            serialNumber
                            expirationDate
                        }
            
                    }
                }
            }
            ';

		return $this->postGraphqlJson($query);
	}

	public function complete_receiving(): TestResponse
	{
		$query = '
			mutation {
			  receivingComplete (input: {id: "1"}) {
			    data {
			      id
			      receivedAt
			      createdAt
			      updatedAt
			      comment
			      location {
			        id
			        name
			      }
			      supplier {
			        id
			        companyName
			      }
			      user {
			        id
			        firstName
			        lastName
			      }
			      receivingItems {
			        id
			        line
			        quantity
			        comment
			        costPrice
			        discountPercentage
			        item {
			          name
			          description
			          unitPrice
			          number
			          costPrice
			          remaining
			        }
			        inventory {
			          id
			          lotNumber
			          serialNumber
			          expirationDate
			        }
			      }
			      receivingStatus {
			        name
			      }
			      items {
			        id
			        name
			      }
			    }
			  }
			}
		';
		return $this->postGraphqlJson($query);
	}

	public function save_details_receiving(): TestResponse
	{
		$query = '
        mutation {
            receivingSaveDetails (input: {receiving: 1, comment: "New Receiving", supplier: 1}) {
                data {
                    id
                    receivedAt
                    createdAt
                    updatedAt
                    comment
                    location {
                        id
                        name
                    }
                    supplier {
                        id
                        companyName
                    }
                    user {
                        id
                        firstName
                        lastName
                    }
                    receivingItems {
                        id
                        line
                        quantity
                        comment
                        costPrice
                        discountPercentage
                        item {
                            name
                            description
                            unitPrice
                            number
                            costPrice
                            remaining
                        }
                        inventory {
                            id
                            lotNumber
                            serialNumber
                            expirationDate
                        }
                    }
                    receivingStatus {
                        name
                    }
                    items {
                        id
                        name
                    }
                }
            }
        }
        ';
		return $this->postGraphqlJson($query);
	}

	public function void_receiving()
	{
		$query = '
                mutation {
                  receivingVoid (input: {id: "1"}) {
                    data {
                      id
                    }
                  }
                }
            ';

		$response = $this->postGraphqlJson($query);
	}

	public function create_multiple_allergies()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$query = 'mutation {
                patientAllergiesCreate (input:[
                    {
                        allergy: "Fabrics",
                        clientId: 1
                    },
                    {
                        allergy: "Feathers",
                        clientId: 1
                    }
                ])
                {
                    data {
                        id
                        allergy
                        updatedAt
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);
	}

	public function tax_create()
	{
		$query = 'mutation {
                taxCreate (input: {
                    id: "",
                    name: "test tax",
                    percent: 77
                }) {
                    data {
                        id,
                        percent
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);
	}

	public function drug_allergy_create()
	{
		$query = 'mutation {
                patientDrugAllergiesCreate (input:[
                    {
                        allergy: "Codeine",
                        clientId: 1
                    }
                ])
                {
                    data {
                        id
                        allergy
                        updatedAt
                    }
                }
            }
      ';

		$response = $this->postGraphqlJson($query);
	}

	public function gift_card_create_with_number()
	{
		//TODO add owner factory
		$query = 'mutation {
                creditCreate (input: {
                    id: "",
                    number: "1006ed46-9aa6-49c3-99ea-ec64cb0ff817",
                    type: "Gift Card",
                    currentValue: 777,
                    originalValue: 777,
                    updatedAt: ""
                }) {
                    data {
                        id
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);
	}

	public function gift_card_create_without_number()
	{
		//TODO add owner factory
		$query = 'mutation {
                creditCreate (input: {
                    id: "",
                    number: "",
                    type: "Gift Card",
                    currentValue: 777,
                    originalValue: 777,
                    updatedAt: ""
                }) {
                    data {
                        id
                    }
                }
            }';

		$response = $this->postGraphqlJson($query);
	}

	public function create_invoice_that_has_total()
	{
		$query = '
            mutation {
                invoiceCreate (input: {id: "", comment: "", printComment: false, rounding: 0, isTaxable: false, total: 5.2, patient: 2, owner: 2, location: 1}) {
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
	}

	public function create_species()
	{
		$query = 'mutation {
                speciesCreate (input: {
                    name: "test species",
                }) {
                    data {
                        id
                        name
                    }
                }
            }
        ';

		$response = $this->postGraphqlJson($query);

		return $response->json("*.speciesCreate.*.*")[0];
	}

	public function create_breed()
	{
		$species = $this->create_species();

		$query = 'mutation BreedCreateMutation($input: breedCreateInput!) {
                    breedCreate(input: $input) {
                        data {
                            id
                            name
                        }
                    }
                }';

		$variables = [
			"input" => [
				"name" => "test breed",
				"species" => $species["name"],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		return $response->json("*.breedCreate.*.*")[0];
	}

	public function create_color()
	{
		$species = $this->create_species();

		$query = 'mutation ColorCreateMutation($input: colorCreateInput!) {
                    colorCreate(input: $input) {
                        data {
                            id
                            name
                        }
                    }
                }';

		$variables = [
			"input" => [
				"name" => "test color",
				"species" => $species["name"],
			],
		];

		$response = $this->postGraphqlJsonWithVariables($query, $variables);

		return $response->json("*.colorCreate.*.*")[0];
	}
}
