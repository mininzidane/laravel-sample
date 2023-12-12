<?php

namespace App\Models;

use App\GraphQL\Types\TaxGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property float $percent
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 */
class Tax extends HippoModel
{
	use HasName;
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = TaxGraphQLType::class;

	protected $table = "taxes";

	protected $primaryKey = "id";

	protected $guarded = ["id"];

	protected $fillable = ["name", "percent"];

	protected $appends = ["relationship_number"];

	public function __construct(array $attributes = [])
	{
		$this->nameFields = ["name"];

		parent::__construct($attributes);
	}

	public function items(): BelongsToMany
	{
		return $this->belongsToMany(Item::class, "item_taxes");
	}

	public function invoiceItemTaxes(): HasMany
	{
		return $this->hasMany(InvoiceItemTax::class);
	}

	// Return the number if items for single foreign relationship
	public function getRelationshipNumberAttribute(): int
	{
		return ItemTax::on($this->getConnectionName())
			->where("tax_id", $this->id)
			->count();
	}
}
