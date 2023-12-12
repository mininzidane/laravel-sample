<?php

namespace Tests\Feature\Factories\PaymentPlatform;

use App\Models\PaymentPlatform;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PaymentPlatformFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var PaymentPlatform $model */
		$model = PaymentPlatform::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"name" => $model->name,
		]);
	}
}
