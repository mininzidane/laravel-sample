<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\PaymentMethod;

use App\Models\PaymentMethod;
use Tests\Helpers\PassportSetupTestCase;

class PaymentMethodFactoryTest extends PassportSetupTestCase
{
	public function test_factory_can_create_data(): void
	{
		/** @var PaymentMethod $model */
		$model = PaymentMethod::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"name" => $model->name,
			"protected" => $model->protected,
			"active" => $model->active,
			"user_facing" => $model->user_facing,
			"is_depositable" => $model->is_depositable,
			"process_type" => $model->process_type,
			"payment_platform_id" => $model->payment_platform_id,
		]);
	}
}
