<?php

namespace Tests\Feature\Factories\ChartOfAccounts;

use App\Models\ChartOfAccounts;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ChartOfAccountsFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var ChartOfAccounts $chartOfAccounts */
		$chartOfAccounts = ChartOfAccounts::factory()->create();

		$this->assertDatabaseHas("chart_of_accounts", [
			"id" => $chartOfAccounts->id,
			"series" => $chartOfAccounts->series,
			"name" => $chartOfAccounts->name,
			"category_id" => $chartOfAccounts->category_id,
		]);
	}
}
