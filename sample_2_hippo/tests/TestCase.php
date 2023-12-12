<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Helpers\TruncateDatabase;

/**
 * @method truncateTestDatabase()
 */
abstract class TestCase extends BaseTestCase
{
	use CreatesApplication;

	protected function setUpTraits(): array
	{
		$uses = parent::setUpTraits();

		if (isset($uses[TruncateDatabase::class])) {
			$this->truncateTestDatabase();
		}

		return $uses;
	}
}
