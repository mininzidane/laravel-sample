<?php

namespace Tests\Feature\Factories\ClearentTerminal;

use App\Models\ClearentTerminal;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ClearentTerminalFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create()
	{
		$terminal = ClearentTerminal::factory()->create();

		$this->assertDatabaseHas($terminal->getTable(), [
			"terminal_id" => $terminal->terminal_id,
			"api_key" => $terminal->api_key,
			"location_id" => $terminal->location_id,
			"payment_platform_id" => $terminal->payment_platform_id,
		]);
	}
}
