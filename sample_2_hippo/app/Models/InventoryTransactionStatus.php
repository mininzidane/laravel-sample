<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\GraphQL\Types\InventoryTransactionStatusGraphQLType;

class InventoryTransactionStatus extends HippoModel
{
	use SoftDeletes;
	use HasName;
	use HasFactory;

	public static $graphQLType = InventoryTransactionStatusGraphQLType::class;

	protected $table = "inventory_transaction_statuses";

	protected $fillable = ["name"];

	public function inventoryTransactions()
	{
		return $this->hasMany(InventoryTransaction::class, "status_id", "id");
	}
}
