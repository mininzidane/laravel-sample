<?php

namespace Tests\Feature\Mutations\PatientVaccination;

use App\Models\User;
use App\Models\Item;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Reminder;
use App\Models\Location;
use App\Models\ReminderInterval;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class PatientVaccinationCreateMutationTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	protected $query = '
		mutation PatientVaccinationCreate($input: patientVaccinationCreateInput!) {
			patientVaccinationCreate (input: $input) {
				data {
					id
					name
					dosage
					administeredAt
					currentGender
					currentWeight
					lotNumber
					serialNumber
					expirationDate
					item {
						id
						name
						isSerialized
					}
					invoice {
						id
						createdAt
					}
					invoiceItem {
						id
					}
					administeredBy {
						id
						firstName
						lastName
						fullName
					}					
					provider {
						id
						firstName
						lastName
						fullName
					}
					locationAdministered {
						id
						name
					}
				}
			}
		}';

	protected $patient;
	protected $item;
	protected $location;
	protected $invoice;

	protected array $variables;

	public function setUp(): void
	{
		parent::setUp();

		$this->patient = Patient::factory()->create();
		$this->item = Item::factory()->create(["is_vaccine" => 1]);
		$this->location = Location::factory()->create();
		$this->invoice = Invoice::factory()->create();

		$this->variables = [
			"input" => [
				"patient" => $this->patient->id,
				"vaccine" => $this->item->id,
				"invoice" => $this->invoice->id,
				"location" => $this->location->id,
				"dosage" => 1,
				"processReminders" => true,
				"allowExcessiveQuantity" => true,
				"provider" => null,
				"administeredBy" => null,
				"administeredDate" => $this->carbonTestTime,
			],
		];
	}

	public function test_vaccine_cannot_be_created_without_provider()
	{
		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$this->variables,
		);

		$this->assertContains(
			"A Valid Provider Must be Selected",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_vaccine_can_be_created()
	{
		$provider = $this->populateProvider();

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$this->variables,
		);

		$response
			->assertStatus(200)
			->assertJsonStructure([
				"data" => [
					"patientVaccinationCreate" => [
						"data" => [
							"*" => [
								"id",
								"name",
								"dosage",
								"administeredAt",
								"administeredBy",
								"currentGender",
								"currentWeight",
								"lotNumber",
								"serialNumber",
								"expirationDate",
								"item" => ["id", "name", "isSerialized"],
								"invoice" => ["id", "createdAt"],
								"invoiceItem" => ["id"],
								"provider" => [
									"id",
									"firstName",
									"lastName",
									"fullName",
								],
								"locationAdministered" => ["name"],
							],
						],
					],
				],
			])
			->assertExactJson([
				"data" => [
					"patientVaccinationCreate" => [
						"data" => [
							[
								"administeredAt" => $this->carbonTestTime->format(
									"Y-m-d",
								),
								"administeredBy" => null,
								"currentGender" => $this->patient->gender,
								"currentWeight" => null,
								"dosage" => 1,
								"expirationDate" => null,
								"id" => "{$this->item->id}",
								"invoice" => [
									"createdAt" => $this->carbonTestTime->format(
										"Y-m-d H:i:s",
									),
									"id" => "{$this->invoice->id}",
								],
								"invoiceItem" => ["id" => "{$this->item->id}"],
								"item" => [
									"id" => "{$this->item->id}",
									"isSerialized" =>
										$this->item->is_serialized,
									"name" => $this->item->name,
								],
								"locationAdministered" => null,
								"lotNumber" => null,
								"name" => $this->item->name,
								"provider" => [
									"firstName" => $provider->first_name,
									"fullName" => $provider->full_name,
									"id" => "{$provider->id}",
									"lastName" => $provider->last_name,
								],
								"serialNumber" => null,
							],
						],
					],
				],
			]);
	}

	public function test_vaccine_with_reminder_populates_reminders()
	{
		$this->populateProvider();
		$item = Item::factory()->create([
			"name" => "A Vaccine with a Reminder",
			"is_vaccine" => 1,
			"reminder_interval_id" => ReminderInterval::inRandomOrder()->first(),
		]);

		$this->variables["input"]["vaccine"] = $item->id;

		$response = $this->postGraphqlJsonWithVariables(
			$this->query,
			$this->variables,
		);

		$response->assertStatus(200);

		$reminder = Reminder::where(
			"item_id",
			$response->decodeResponseJson()["data"]["patientVaccinationCreate"][
				"data"
			][0]["item"]["id"],
		)->first();

		$this->assertEquals($item->id, $reminder->item_id);
		$this->assertEquals($item->name, $reminder->description);
		$this->assertNotNull($reminder->due_date);
	}

	protected function populateProvider()
	{
		$provider = User::factory()->create();

		$this->variables["input"]["provider"] = $provider->id;

		return $provider;
	}
}
