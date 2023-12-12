<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\GraphQL\Types\AccountCategoryGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\AccountCategory
 *
 * @property int $id
 * @property string $name
 * @property int $parent_category_id
 * @property AccountCategory $parent_category
 */
class AccountCategory extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = AccountCategoryGraphQLType::class;

	protected $table = "account_categories";

	protected $fillable = ["name", "parent_category_id"];

	public function parentCategory()
	{
		return $this->belongsTo(
			AccountCategory::class,
			"parent_category_id",
			"id",
		);
	}

	public function childCategories()
	{
		return $this->hasMany(
			AccountCategory::class,
			"parent_category_id",
			"id",
		);
	}

	public function chartOfAccounts()
	{
		return $this->hasMany(ChartOfAccounts::class, "category_id", "id");
	}
}
