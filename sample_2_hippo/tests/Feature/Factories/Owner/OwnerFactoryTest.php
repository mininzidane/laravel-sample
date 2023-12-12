<?php

namespace Tests\Feature\Factories\Owner;

use App\Models\Owner;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class OwnerFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Owner $model */
		$model = Owner::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"organization_id" => $model->organization_id,
			"first_name" => $model->first_name,
			"middle_name" => $model->middle_name,
			"last_name" => $model->last_name,
			"address1" => $model->address1,
			"address2" => $model->address2,
			"city" => $model->city,
			"state" => $model->state,
			"zip" => $model->zip,
			"country" => $model->country,
			"phone" => $model->phone,
			"email" => $model->email,
			"notes" => $model->notes,
			"refer" => $model->refer,
			"primary" => $model->primary,
			"alias_id" => $model->alias_id,
			"removed" => $model->removed,
			"dob" => $model->dob,
			"phone_2" => $model->phone_2,
			"phone_3" => $model->phone_3,
			"dl_number" => $model->dl_number,
			"communication_preference" => $model->communication_preference,
			"taxable" => $model->taxable,
		]);

		// Check that appends attribute is set
		$this->assertEquals(0, $model->balance);
		$this->assertEquals(
			"$model->first_name $model->last_name",
			$model->full_name,
		);
		$this->assertEquals(0, $model->accountCreditTotal);
		$this->assertEquals(
			[
				"amount" => 0,
				"received_at" => "",
			],
			$model->last_payment,
		);
	}
}
