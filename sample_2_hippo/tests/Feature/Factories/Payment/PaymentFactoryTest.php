<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Payment;

use App\Models\Payment;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PaymentFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Payment $model */
		$model = Payment::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"owner_id" => $model->owner_id,
			"payment_method_id" => $model->payment_method_id,
			"amount" => $model->amount,
			"received_at" => $model->received_at,
			"payment_platform_id" => $model->payment_platform_id,
			"credit_id" => $model->credit_id,
		]);
	}
}
