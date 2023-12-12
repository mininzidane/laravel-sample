<?php

namespace Tests\Feature\Mutations\Item;

use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Location;
use App\Models\ReminderInterval;
use App\Models\Supplier;
use App\Models\Tax;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ItemCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers, WithFaker;

	protected const QUERY = '
            mutation ItemCreate($unitPriceDisabled: Boolean!, $input: ItemCreateInput!) {
                itemCreate(unitPriceDisabled: $unitPriceDisabled, input: $input) {
                    data {
                        id
                    }
                }
            }';

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
	public function test_item_can_be_created(array $input)
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
					"itemCreate" => [
						"data" => [
							"*" => [],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"itemCreate" => [
						"data" => [["id" => "1"]],
					],
				],
			]);
	}

	public function validationFailedDataProvider(): array
	{
		return [
			"Empty Item name" => [
				"variablesInput" => ["name" => null],
				"errorMessage" => "Item name is required",
			],
			"Too long Item name" => [
				"variablesInput" => ["name" => str_repeat("A", 256)],
				"errorMessage" =>
					"Item name must be smaller than 255 characters",
			],
			"Empty ItemType id" => [
				"variablesInput" => ["itemTypeId" => null],
				"errorMessage" => "A type must be chosen",
			],
			"Empty Location id" => [
				"variablesInput" => ["itemLocations" => []],
				"errorMessage" => "A location must be chosen",
			],
			"Empty unit price" => [
				"variablesInput" => ["unitPrice" => null],
				"errorMessage" => "Unit price is required",
			],
			"Unit price smaller than 0" => [
				"variablesInput" => ["unitPrice" => -1],
				"errorMessage" =>
					"Unit price must be greater than or equal to 0",
			],
			"Unit price bigger than 999,999,999" => [
				"variablesInput" => ["markupPercentage" => 1000 * 1000 * 1000],
				"errorMessage" =>
					"Markup percentage must be less than 999,999,999",
			],
			"Incorrect Cost price type" => [
				"variablesInput" => ["costPrice" => null],
				"errorMessage" => "The input.cost price must be a number.",
			],
			"Incorrect Minimum sale amount type" => [
				"variablesInput" => ["minimumSaleAmount" => null],
				"errorMessage" =>
					"The input.minimum sale amount must be a number.",
			],
			"Incorrect Dispensing fee type" => [
				"variablesInput" => ["dispensingFee" => null],
				"errorMessage" => "The input.dispensing fee must be a number.",
			],
		];
	}

	/**
	 * @dataProvider validationFailedDataProvider
	 */
	public function test_item_cant_be_created_with_failed_validation(
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
		$this->itemTax = Tax::factory()->create();
		$this->manufacturer = Supplier::factory()->create();
	}

	private function setUpVariables(): void
	{
		$this->variables = [
			"input" => [
				"name" => "test item",
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
