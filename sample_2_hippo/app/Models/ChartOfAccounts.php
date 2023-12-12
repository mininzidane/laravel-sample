<?php

namespace App\Models;

use App\GraphQL\Types\ChartOfAccountGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ChartOfAccounts
 *
 * @property int $id
 * @property string $series
 * @property string $name
 * @property int $category_id
 */
class ChartOfAccounts extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = ChartOfAccountGraphQLType::class;
	protected $table = "chart_of_accounts";

	protected $fillable = ["series", "name", "category_id"];

	public function accountCategory()
	{
		return $this->belongsTo(AccountCategory::class, "category_id", "id");
	}

	public function items()
	{
		return $this->hasMany(Item::class, "account_id", "id");
	}

	public function invoiceItems()
	{
		return $this->hasMany(InvoiceItem::class, "account_id", "id");
	}
}
