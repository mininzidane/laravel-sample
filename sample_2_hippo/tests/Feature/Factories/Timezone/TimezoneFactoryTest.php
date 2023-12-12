<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Timezone;

use App\Models\Timezone;
use Tests\Helpers\PassportSetupTestCase;

class TimezoneFactoryTest extends PassportSetupTestCase
{
	public function test_factory_can_create_data(): void
	{
		/** @var Timezone $model */
		$model = Timezone::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"value" => $model->value,
			"abbr" => $model->abbr,
			"offset" => $model->offset,
			"isdst" => $model->isdst,
			"text" => $model->text,
			"php_supported" => $model->php_supported,
		]);
	}
}
