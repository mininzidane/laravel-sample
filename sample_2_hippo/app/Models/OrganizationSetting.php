<?php

namespace App\Models;

use App\GraphQL\Types\OrganizationSettingGraphQLType;

/**
 * App\Models\OrganizationSetting
 *
 * @property string $setting_name
 * @property string $setting_value
 */
class OrganizationSetting extends HippoModel
{
	protected $table = "tblOrganizationSettings";

	public static $graphQLType = OrganizationSettingGraphQLType::class;

	protected $primaryKey = "setting_name";
	public $incrementing = false;
	protected $keyType = "string";

	protected $fillable = ["setting_name", "setting_value"];
}
