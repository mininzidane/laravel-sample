<?php

namespace App\Models;

use App\GraphQL\Types\ItemTypeGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ItemType
 *
 * @property int $id
 * @property string $name
 * @property bool $process_inventory
 *
 * @mixin \Eloquent
 */

class ItemType extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemTypeGraphQLType::class;

	protected $table = "item_types";

	protected $fillable = ["name"];

	public const ITEM_KIT = 6;
	public const RABIES_TAG = 7;
	public const GIFT_CARD = 9;
	public const ACCOUNT_CREDIT = 10;

	public function items(): HasMany
	{
		return $this->hasMany(Item::class, "type_id", "id");
	}

	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class, "type_id", "id");
	}
}
