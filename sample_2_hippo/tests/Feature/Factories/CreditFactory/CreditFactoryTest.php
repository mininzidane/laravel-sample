<?php

namespace Tests\Feature\Factories\CreditFactory;

use App\Models\Credit;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class CreditFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var Credit $credit */
		$credit = Credit::factory()->create();

		$this->assertDatabaseHas("credits", [
			"id" => $credit->id,
			"type" => $credit->type,
			"number" => $credit->number,
			"original_value" => $credit->original_value,
			"value" => $credit->value,
			"owner_id" => $credit->owner_id,
		]);
	}
}
