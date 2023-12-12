<?php

namespace Tests\Feature\Mutations\Item;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Location;
use App\Models\ReminderInterval;
use App\Models\Supplier;
use App\Models\Tax;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemUpdateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected const QUERY = 'mutation itemUpdateMutation($id: ID, $unitPriceDisabled: Boolean!, $input: ItemUpdateInput!) {
                itemUpdate(id: $id, unitPriceDisabled: $unitPriceDisabled, input: $input) {
                    data {
                        id,
                        name,
                        unitPrice,
                        itemTypeId                      
                    }
                }
            }';

	private Item $item;
	private ItemType $itemType;
	private ItemCategory $itemCategory;
	private ReminderInterval $reminderInterval;
	private Location $itemLocation;
	private Tax $itemTax;
	private Supplier $manufacturer;

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
					"number" => null,
					"chartOfAccount" => null,
					"categoryId" => null,
					"description" => null,
					"reminderIntervalId" => null,
					"reminderReplaces" => [],
					"minimumOnHand" => null,
					"maximumOnHand" => null,
					"nextTagNumber" => null,
					"drugIdentifier" => null,
					"itemSpeciesRestrictions" => [],
					"itemTaxes" => [],
					"itemVolumePricing" => [],
					"itemKitItems" => [],
					"manufacturerId" => null,
				],
			],
			"Full data" => ["input" => []],
		];
	}

	/**
	 * @dataProvider creationDataProvider
	 */
	public function test_item_can_be_updated(array $input)
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
				"itemUpdate" => [
					"data" => [
						"*" => ["id", "name", "unitPrice", "itemTypeId"],
					],
				],
			],
		]);
		$response->assertExactJson([
			"data" => [
				"itemUpdate" => [
					"data" => [
						[
							"id" => (string) $this->item->id,
							"name" => $this->variables["input"]["name"],
							"unitPrice" =>
								$this->variables["input"]["unitPrice"],
							"itemTypeId" => $this->itemType->id,
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
				"variablesInput" => ["input" => ["name" => null]],
				"errorMessage" => "Item name is required",
			],
			"Too long Item name" => [
				"variablesInput" => [
					"input" => ["name" => str_repeat("A", 256)],
				],
				"errorMessage" =>
					"Item name must be smaller than 255 characters",
			],
			"Empty ItemType id" => [
				"variablesInput" => ["input" => ["itemTypeId" => null]],
				"errorMessage" => "A type must be chosen",
			],
			"Empty Location id" => [
				"variablesInput" => ["input" => ["itemLocations" => []]],
				"errorMessage" => "A location must be chosen",
			],
			"Empty unit price" => [
				"variablesInput" => ["input" => ["unitPrice" => null]],
				"errorMessage" => "Unit price is required",
			],
			"Unit price smaller than 0" => [
				"variablesInput" => ["input" => ["unitPrice" => -1]],
				"errorMessage" =>
					"Unit price must be greater than or equal to 0",
			],
			"Unit price bigger than 999,999,999" => [
				"variablesInput" => [
					"input" => ["markupPercentage" => 1000 * 1000 * 1000],
				],
				"errorMessage" =>
					"Markup percentage must be less than 999,999,999",
			],
			"Incorrect Cost price type" => [
				"variablesInput" => ["input" => ["costPrice" => null]],
				"errorMessage" => "The input.cost price must be a number.",
			],
			"Incorrect Minimum sale amount type" => [
				"variablesInput" => ["input" => ["minimumSaleAmount" => null]],
				"errorMessage" =>
					"The input.minimum sale amount must be a number.",
			],
			"Incorrect Dispensing fee type" => [
				"variablesInput" => ["input" => ["dispensingFee" => null]],
				"errorMessage" => "The input.dispensing fee must be a number.",
			],
		];
	}

	/**
	 * @dataProvider validationFailedDataProvider
	 */
	public function test_item_cant_be_updated_with_failed_validation(
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
				"errorMessage" => "Internal server error",
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
	public function test_item_cant_be_updated_wrong_id(
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

	public function test_deleted_item_cant_be_updated()
	{
		/** @var Item $deletedItem */
		$deletedItem = Item::factory()->create(["deleted_at" => now()]);
		$this->variables["id"] = $deletedItem->id;

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
		$this->itemType = ItemType::all()
			->random()
			->first();
		$this->itemCategory = ItemCategory::factory()->create();
		$this->reminderInterval = ReminderInterval::all()
			->random()
			->first();
		$this->itemLocation = Location::factory()->create();
		$this->manufacturer = Supplier::factory()->create();
		$this->itemTax = Tax::factory()->create();

		$this->item = Item::factory()->create([
			"category_id" => $this->itemCategory->id,
			"type_id" => $this->itemType->id,
		]);
	}

	private function setUpVariables(): void
	{
		$this->variables = [
			"id" => $this->item->id,
			"input" => [
				"name" => "Test name changed",
				"number" => "123453",
				"itemTypeId" => $this->itemType->id,
				"chartOfAccount" => "2",
				"categoryId" => $this->itemCategory->id,
				"description" => "",
				"allowAltDescription" => false,
				"isVaccine" => false,
				"isPrescription" => false,
				"isSerialized" => false,
				"isControlledSubstance" => false,
				"isInWellnessPlan" => false,
				"isEuthanasia" => false,
				"isReproductive" => false,
				"requiresProvider" => false,
				"hideFromRegister" => false,
				"costPrice" => 0,
				"minimumSaleAmount" => 0,
				"markupPercentage" => 0,
				"dispensingFee" => 0,
				"unitPrice" => 0,
				"isNonTaxable" => false,
				"applyToRemainder" => false,
				"reminderIntervalId" => $this->reminderInterval->id,
				"reminderReplaces" => [],
				"minimumOnHand" => 0,
				"maximumOnHand" => 0,
				"nextTagNumber" => null,
				"drugIdentifier" => "",
				"isSingleLineKit" => false,
				"itemSpeciesRestrictions" => [],
				"itemLocations" => [
					[
						"id" => $this->itemLocation->id,
					],
				],
				"itemTaxes" => [
					[
						"id" => $this->itemTax->id,
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
				"manufacturerId" => $this->manufacturer->id,
			],
			"unitPriceDisabled" => false,
		];
	}
}
