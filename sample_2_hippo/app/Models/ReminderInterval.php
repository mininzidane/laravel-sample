<?php

namespace App\Models;

use App\GraphQL\Types\ReminderIntervalGraphQLType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReminderInterval
 *
 * @property-read int $id
 *
 * @property string $code
 * @property string $name
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Collection|Item[] $items
 * @property-read Collection|InvoiceItem[] $invoiceItems
 */
class ReminderInterval extends HippoModel
{
	use SoftDeletes, HasFactory;

	public static $graphQLType = ReminderIntervalGraphQLType::class;

	protected $table = "reminder_intervals";

	protected $fillable = ["code", "name"];

	public function items(): HasMany
	{
		return $this->hasMany(Item::class);
	}

	public function invoiceItems(): HasMany
	{
		return $this->hasMany(InvoiceItem::class);
	}
}
