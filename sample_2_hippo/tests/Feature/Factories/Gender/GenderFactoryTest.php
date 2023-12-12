<?php

namespace Tests\Feature\Factories\Gender;

use App\Models\Gender;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class GenderFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Gender $model */
		$model = Gender::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"gender" => $model->gender,
			"sex" => $model->sex,
			"neutered" => $model->neutered,
			"species" => $model->species,
		]);
	}
}
