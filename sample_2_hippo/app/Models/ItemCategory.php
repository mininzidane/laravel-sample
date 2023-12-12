<?php

namespace App\Models;

use App\GraphQL\Types\ItemCategoryGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ItemCategory
 *
 * @property int $id
 * @property string $name
 * @property bool $process_inventory
 *
 * @mixin \Eloquent
 */
class ItemCategory extends HippoModel
{
	use HasName;
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemCategoryGraphQLType::class;

	protected $table = "item_categories";

	protected $fillable = ["name"];

	protected $appends = ["relationship_number"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function items(): HasMany
	{
		return $this->hasMany(Item::class, "category_id", "id");
	}

	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class, "category_id", "id");
	}

	// Return the number if items for single foreign relationship
	public function getRelationshipNumberAttribute(): int
	{
		return Item::on($this->getConnectionName())
			->where("category_id", $this->id)
			->count();
	}

	//Use this for multi table search
	//    public function getRelationshipArrayAttribute()
	//    {
	//
	//        $id = $this->id;
	//
	//        $posts = $this->withCount(['items','invoiceItems' => function (Builder $query) use ($id) {
	//            $query->where('category_id', '=', $id);
	//        }])
	//            ->get()
	//            ->first(function($item) use ($id) {
	//                return $item->id == $id;
	//            });
	//        $arr = [];
	//        $arr['items'] = $posts->items_count;
	//        $arr['invoiceItems'] = $posts->invoiceItems_count;
	//
	//        return $arr;
	//    }
}
