<?php

namespace Tests\Feature\Factories\AccountCategory;

use App\Models\AccountCategory;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class AccountCategoryFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		$category = AccountCategory::factory()->create();

		$this->assertDatabaseHas("account_categories", [
			"id" => $category->id,
			"name" => $category->name,
			"parent_category_id" => $category->parent_category_id,
		]);
	}
}
