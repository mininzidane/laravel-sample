<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\PatientField;
use App\Models\PatientImage;
use GraphQL\Type\Definition\Type;

class PatientImageGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "patientImage";

	protected $attributes = [
		"name" => "PatientImage",
		"description" => "An image associated with a patient",
		"model" => PatientImage::class,
	];

	public function columns(): array
	{
		return [
			"clientId" => [
				"type" => Type::int(),
				"description" => "The id of the alert",
				"alias" => "client_id",
			],
			"name" => [
				"type" => Type::string(),
				"description" => "The S3 path for the image",
			],
			"presignedUrl" => [
				"type" => Type::string(),
				"selectable" => false,
			],
			"patient" => (new PatientField([
				"description" => "The patient this image is associated with",
			]))->toArray(),
		];
	}
}
