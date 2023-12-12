<?php

namespace Tests\Feature\Factories\PaymentPlatformActivation;

use App\Models\PaymentPlatformActivation;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PaymentPlatformActivationFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var PaymentPlatformActivation $model */
		$model = PaymentPlatformActivation::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"payment_platform_id" => $model->payment_platform_id,
			"location_id" => $model->location_id,
			"mode" => $model->mode,
			"info" => $model->info,
			"is_active" => $model->is_active,
		]);
	}
}
