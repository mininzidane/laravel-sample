<?php

namespace App\GraphQL\Types;

use App\GraphQL\Fields\OrganizationField;
use App\Models\SchedulerSettings;
use GraphQL\Type\Definition\Type;

class SchedulerSettingsGraphQLType extends HippoGraphQLType
{
	public static $graphQLType = "schedulerSettings";

	protected $attributes = [
		"name" => "SchedulerSettings",
		"description" => 'The settings for an organization\'s scheduler',
		"model" => SchedulerSettings::class,
	];

	public function columns(): array
	{
		return [
			"id" => [
				"type" => Type::nonNull(Type::string()),
				"description" => "The id of the setting record",
			],
			"startTime" => [
				"type" => Type::string(),
				"description" => "The first available normal appointment time",
				"alias" => "start_time",
			],
			"endTime" => [
				"type" => Type::string(),
				"description" => "The last available normal appointment time",
				"alias" => "end_time",
			],
			"unit" => [
				"type" => Type::string(),
				"description" => "The standard appointment length in minutes",
			],
			"maxDuration" => [
				"type" => Type::string(),
				"description" => "The maximum appointment duration in minutes",
				"alias" => "max_duration",
			],
			"organization" => (new OrganizationField([
				"description" =>
					"The organization for which these settings apply",
			]))->toArray(),
		];
	}
}
