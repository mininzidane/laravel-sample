<?php

namespace Tests\Feature\Factories\Patient;

use App\Models\Patient;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Patient $model */
		$model = Patient::factory()->create();

		$this->assertDatabaseHas("tblClients", [
			"id" => $model->id,
			"organization_id" => $model->organization_id,
			"first_name" => $model->first_name,
			"middle_name" => $model->middle_name,
			"last_name" => $model->last_name,
			"prefix" => $model->prefix,
			"suffix" => $model->suffix,
			"date_of_birth" => $model->date_of_birth,
			"date_of_death" => $model->date_of_death,
			"gender_id" => $model->gender_id,
			"species" => $model->species,
			"marking" => $model->marking,
			"ethnicity" => $model->ethnicity,
			"race" => $model->race,
			"ssn" => $model->ssn,
			"address1" => $model->address1,
			"address2" => $model->address2,
			"address3" => $model->address3,
			"city" => $model->city,
			"state" => $model->state,
			"zip" => $model->zip,
			"county" => $model->county,
			"timezone" => $model->timezone,
			"drivers_license_state" => $model->drivers_license_state,
			"drivers_license_number" => $model->drivers_license_number,
			"home_phone" => $model->home_phone,
			"work_phone" => $model->work_phone,
			"work_phone_ext" => $model->work_phone_ext,
			"cell_phone" => $model->cell_phone,
			"fax" => $model->fax,
			"prefered_communication" => $model->prefered_communication,
			"email" => $model->email,
			"language" => $model->language,
			"phr" => $model->phr,
			"notes" => $model->notes,
			"added_by" => $model->added_by,
			"license" => $model->license,
			"microchip" => $model->microchip,
			"alias_id" => $model->alias_id,
			"vcp_contract_id" => $model->vcp_contract_id,
			"removed" => $model->removed,
			"history_last_modified" => $model->history_last_modified,
			"history_last_modified_by" => $model->history_last_modified_by,
		]);

		$primaryOwner = null;
		foreach ($model->owners as $owner) {
			if ($owner->pivot->primary) {
				$primaryOwner = $owner;
			}
		}

		// Check that appends attribute is set
		$this->assertEquals(null, $model->currentWeight);
		$this->assertEquals(null, $model->lastVet);
		$this->assertEquals(null, $model->rabies);
		$this->assertEquals("img/hippo-avatar.svg", $model->primaryImage);
		$this->assertEquals(null, $model->lastVisit);
		$this->assertEquals($primaryOwner, $model->primaryOwner);
		$this->assertEquals("N/A", $model->formattedAge);
	}
}
