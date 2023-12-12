<?php

declare(strict_types=1);

namespace Tests\Feature\Factories\Resource;

use App\Models\Resource;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class ResourceFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data(): void
	{
		/** @var Resource $resource */
		$resource = Resource::factory()->create();

		$this->assertDatabaseHas($resource->getTable(), [
			"user_id" => $resource->user->id,
			"organization_id" => $resource->organization->id,
			"location_id" => $resource->location->id,
			"name" => $resource->name,
			"description" => $resource->description,
			"color" => $resource->color,
		]);
	}
}
