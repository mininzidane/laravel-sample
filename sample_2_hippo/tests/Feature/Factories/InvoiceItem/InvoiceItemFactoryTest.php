<?php

namespace Tests\Feature\Factories\InvoiceItem;

use App\Models\InvoiceItem;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class InvoiceItemFactoryTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_factory_can_create_data()
	{
		/** @var InvoiceItem $model */
		$model = InvoiceItem::factory()->create();

		$this->assertDatabaseHas($model->getTable(), [
			"id" => $model->id,
			"invoice_id" => $model->invoice_id,
			"provider_id" => $model->provider_id,
			"chart_id" => $model->chart_id,
			"chart_type" => $model->chart_type,
			"line" => $model->line,
			"quantity" => $model->quantity,
			"name" => $model->name,
			"number" => $model->number,
			"price" => $model->price,
			"discount_percent" => $model->discount_percent,
			"discount_amount" => $model->discount_amount,
			"total" => $model->total,
			"serial_number" => $model->serial_number,
			"administered_date" => $model->administered_date,
			"type_id" => $model->type_id,
			"category_id" => $model->category_id,
			"account_id" => $model->account_id,
			"description" => $model->description,
			"allow_alt_description" => $model->allow_alt_description,
			"cost_price" => $model->cost_price,
			"volume_price" => $model->volume_price,
			"volume_quantity" => $model->volume_quantity,
			"apply_discount_to_remainder" =>
				$model->apply_discount_to_remainder,
			"markup_percentage" => $model->markup_percentage,
			"unit_price" => $model->unit_price,
			"minimum_sale_amount" => $model->minimum_sale_amount,
			"dispensing_fee" => $model->dispensing_fee,
			"is_vaccine" => $model->is_vaccine,
			"is_prescription" => $model->is_prescription,
			"is_serialized" => $model->is_serialized,
			"is_controlled_substance" => $model->is_controlled_substance,
			"is_euthanasia" => $model->is_euthanasia,
			"is_reproductive" => $model->is_reproductive,
			"hide_from_register" => $model->hide_from_register,
			"requires_provider" => $model->requires_provider,
			"is_in_wellness_plan" => $model->is_in_wellness_plan,
			"vcp_item_id" => $model->vcp_item_id,
			"drug_identifier" => $model->drug_identifier,
			"belongs_to_kit_id" => $model->belongs_to_kit_id,
			"is_single_line_kit" => $model->is_single_line_kit,
			"receiving_item_id" => $model->receiving_item_id,
			"credit_id" => $model->credit_id,
			"old_sale_item_id" => $model->old_sale_item_id,
			"item_id" => $model->item_id,
			"item_kit_id" => $model->item_kit_id,
		]);
	}
}
