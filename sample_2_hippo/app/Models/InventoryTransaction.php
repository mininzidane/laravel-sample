<?php

namespace App\Models;

use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Types\InventoryTransactionGraphQLType;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;

/**
 * App\Models\InventoryTransaction
 *
 * @property-read int $id
 *
 * @property int $inventory_id
 * @property int $user_id
 * @property int $invoice_item_id
 * @property int $status_id
 * @property float $quantity
 * @property string $comment
 * @property Carbon $transaction_at
 * @property int $is_shrink
 * @property null|string $shrink_reason
 *
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon $deleted_at
 *
 * @property-read Inventory|null $inventory
 * @property-read User $user
 * @property-read InvoiceItem|null $invoiceItem
 * @property-read InventoryTransactionStatus $inventoryTransactionStatus
 *
 * @mixin Eloquent
 */
class InventoryTransaction extends HippoModel
{
	use SoftDeletes;
	use HasFactory;

	public static $graphQLType = InventoryTransactionGraphQLType::class;

	protected $table = "inventory_transactions";
	private ?array $unitPriceData = null;

	protected $fillable = [
		"inventory_id",
		"user_id",
		"invoice_item_id",
		"status_id",
		"quantity",
		"comment",
		"transaction_at",
		"is_shrink",
		"shrink_reason",
	];

	private const ACTION_CODES = [
		HippoGraphQLActionCodes::INVOICE_ITEM_BULK_ADD,
		HippoGraphQLActionCodes::INVOICE_ITEM_CREATE,
		HippoGraphQLActionCodes::INVOICE_ITEM_UPDATE,
		HippoGraphQLActionCodes::ITEM_CREATE,
		HippoGraphQLActionCodes::ITEM_UPDATE,
		HippoGraphQLActionCodes::RECEIVING_ITEM_CREATE,
		HippoGraphQLActionCodes::RECEIVING_COMPLETE,
		HippoGraphQLActionCodes::RECEIVING_SAVE_DETAILS,
	];

	public function inventory(): BelongsTo
	{
		return $this->belongsTo(Inventory::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function invoiceItem(): BelongsTo
	{
		return $this->belongsTo(InvoiceItem::class);
	}

	public function inventoryTransactionStatus(): BelongsTo
	{
		return $this->belongsTo(
			InventoryTransactionStatus::class,
			"status_id",
			"id",
		);
	}

	public function getUnitPriceData(): array
	{
		if ($this->unitPriceData === null) {
			$this->unitPriceData = $this->buildUnitPriceData($this->id);
		}

		return $this->unitPriceData;
	}

	private function buildUnitPriceData($transactionId): array
	{
		$userData = $this->getUserData($transactionId);
		$unitPrice = 0;
		$additionalComment = null;

		if ($userData !== null) {
			[
				$unitPrice,
				$additionalComment,
			] = $this->determineUnitPriceByAction(
				$userData->action_id,
				$userData->affected_id,
			);
		}

		return [
			"changedBy" =>
				$userData !== null
					? "{$userData->first_name} {$userData->last_name}"
					: "",
			"unitPrice" => $unitPrice,
			"additionalComment" => $additionalComment,
		];
	}

	/**
	 * @return object{first_name: string, last_name: string, action_id: int, affected_id: int|null}|null
	 */
	private function getUserData(int $transactionId): ?object
	{
		$connectionName = $this->getConnectionName();
		/** @var object{first_name: string, last_name: string, action_id: int, affected_id: int|null}|null $userData */
		$userData = \DB::connection($connectionName)
			->query()
			->select([
				"tblUsers.first_name",
				"tblUsers.last_name",
				"tblLog.action_id",
				"tblLog.affected_id",
			])
			->from("tblUsers")
			->join("tblLog", "tblLog.user_id", "=", "tblUsers.id")
			->leftJoin(
				"inventory",
				"inventory.receiving_item_id",
				"=",
				"tblLog.affected_id",
			)
			->leftJoin(
				"invoice_items",
				"invoice_items.id",
				"=",
				"tblLog.affected_id",
			)
			->leftJoin(
				"inventory AS i2",
				"i2.item_id",
				"=",
				"tblLog.affected_id",
			)
			->leftJoin(
				"inventory_transactions",
				fn(JoinClause $join) => $join
					->on(
						"inventory_transactions.inventory_id",
						"=",
						"inventory.id",
					)
					->orOn(
						"inventory_transactions.invoice_item_id",
						"=",
						"invoice_items.id",
					)
					->orOn("inventory_transactions.inventory_id", "=", "i2.id"),
			)
			->whereIn("tblLog.action_id", self::ACTION_CODES)
			->where("inventory_transactions.id", "=", $transactionId)
			->orderBy("tblLog.timestamp", "DESC")
			->first();
		return $userData;
	}

	/**
	 * @return array<string, string>
	 */
	private function determineUnitPriceByAction($actionId, $affectedId): array
	{
		$unitPrice = 0;
		$additionalComment = null;

		switch ($actionId) {
			case HippoGraphQLActionCodes::RECEIVING_ITEM_CREATE:
			case HippoGraphQLActionCodes::RECEIVING_COMPLETE:
			case HippoGraphQLActionCodes::RECEIVING_SAVE_DETAILS:
				/** @var ReceivingItem $receivingItem */
				$receivingItem = ReceivingItem::on(
					$this->getConnectionName(),
				)->find($affectedId);
				if ($receivingItem !== null) {
					$unitPrice = $receivingItem->unit_price;
					$additionalComment = "Receiving ID: {$affectedId}";
				}
				break;
			case HippoGraphQLActionCodes::INVOICE_ITEM_BULK_ADD:
			case HippoGraphQLActionCodes::INVOICE_ITEM_CREATE:
			case HippoGraphQLActionCodes::INVOICE_ITEM_UPDATE:
				/** @var InvoiceItem $invoiceItem */
				$invoiceItem = InvoiceItem::on(
					$this->getConnectionName(),
				)->find($affectedId);
				if ($invoiceItem !== null) {
					$unitPrice = $invoiceItem->unit_price;
					$additionalComment = "Invoice ID: {$affectedId}";
				}
				break;
			case HippoGraphQLActionCodes::ITEM_CREATE:
			case HippoGraphQLActionCodes::ITEM_UPDATE:
				/** @var Item $item */
				$item = Item::on($this->getConnectionName())->find($affectedId);
				if ($item !== null) {
					$unitPrice = $item->unit_price;
					$additionalComment = "Item ID: {$affectedId}";
				}
				break;
		}

		return [$unitPrice, $additionalComment];
	}
}
