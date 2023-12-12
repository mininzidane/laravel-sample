<?php

namespace Tests\Feature\Factories\PatientImage;

use App\Models\PatientImage;
use Illuminate\Support\Facades\Storage;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class PatientImageFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var PatientImage $model */
		$model = PatientImage::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"client_id" => $model->client_id,
			"image_type" => $model->image_type,
			"size" => $model->size,
			"image_ctgy" => $model->image_ctgy,
			"name" => $model->name,
			"path" => $model->path,
			"description" => $model->description,
			"removed" => $model->removed,
			"organization_id" => $model->organization_id,
		]);

		$this->assertContains(
			Storage::disk("s3-photos")->url($model->name),
			$model->presignedUrl,
		);
	}
}
