<?php

namespace App\Models;

use App\GraphQL\Types\ItemTaxGraphQLType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $item_id
 * @property int $tax_id
 *
 * @property-read Item $item
 * @property-read Tax $tax
 */
class ItemTax extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = ItemTaxGraphQLType::class;

	protected $table = "item_taxes";

	protected $fillable = ["item_id", "tax_id"];

	public function item(): BelongsTo
	{
		return $this->belongsTo(Item::class);
	}

	public function tax(): BelongsTo
	{
		return $this->belongsTo(Tax::class);
	}
}
