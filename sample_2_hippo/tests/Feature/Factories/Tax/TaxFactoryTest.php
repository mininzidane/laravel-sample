<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Tax;

use App\Models\Tax;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class TaxFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Tax $model */
		$model = Tax::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"name" => $model->name,
			"percent" => $model->percent,
		]);
	}
}
