<?php

namespace App\GraphQL\Requests\Mutations\App\Item;

use App\Exceptions\SubdomainNotConfiguredException;
use App\GraphQL\HippoGraphQLActionCodes;
use App\GraphQL\Requests\Mutations\App\AppHippoMutation;
use App\Models\Item;
use App\Models\ItemKitItem;
use App\Models\ItemLocation;
use App\Models\ItemReminderReplaces;
use App\Models\ItemSpeciesRestriction;
use App\Models\ItemTax;
use App\Models\ItemType;
use App\Models\ItemVolumePricing;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ItemUpdateMutation extends AppHippoMutation
{
	protected $model = Item::class;

	protected $permissionName = "Items: Update";

	protected $attributes = [
		"name" => "ItemUpdateMutation",
	];

	protected $actionId = HippoGraphQLActionCodes::ITEM_UPDATE;

	public function args(): array
	{
		return [
			"id" => [
				"name" => "id",
				"type" => Type::id(),
			],
			"unitPriceDisabled" => [
				"name" => "unitPriceDisabled",
				"type" => Type::boolean(),
			],
			"input" => [
				"type" => GraphQL::type("ItemUpdateInput"),
			],
		];
	}

	public function validationErrorMessages($args = []): array
	{
		return [
			"input.name.required" => "Item name is required",
			"input.name.max" => "Item name must be smaller than 255 characters",
			"input.itemTypeId.required" => "A type must be chosen",
			"input.itemLocations.required" => "A location must be chosen",
			"input.unitPrice.required" => "Unit price is required",
			"input.unitPrice.gte" =>
				"Unit price must be greater than or equal to 0",
			"input.markupPercentage.lte" =>
				"Markup percentage must be less than 999,999,999",
		];
	}

	public function rules(array $args = []): array
	{
		return [
			"unitPriceDisabled" => ["required", "boolean"],
			"input.name" => ["required", "max:255"],
			"input.itemTypeId" => [
				"required",
				function ($attribute, $value, $fail) {
					if ($value === ItemType::ACCOUNT_CREDIT) {
						if (
							Item::on(request()->header("Subdomain"))
								->where("type_id", ItemType::ACCOUNT_CREDIT)
								->count()
						) {
							$fail(
								"Only one item can be set as type of account credit",
							);
						}
					}
				},
			],
			"input.itemLocations" => ["required"],
			"input.unitPrice" => [
				"exclude_if:unitPriceDisabled,true",
				"required",
				"gte:0",
			],
			"input.markupPercentage" => ["lte:999999999"],
		];
	}

	/**
	 * @param $root
	 * @param $args
	 * @param $context
	 * @param ResolveInfo $resolveInfo
	 * @param Closure $getSelectFields
	 * @return |null
	 * @throws SubdomainNotConfiguredException
	 */
	public function resolveTransaction(
		$root,
		$args,
		$context,
		ResolveInfo $resolveInfo,
		Closure $getSelectFields
	) {
		$modelInstance = $this->model
			::on($this->subdomainName)
			->findorFail($this->args["id"]);

		$id = $modelInstance->fill($this->args["input"])->id;
		$modelInstance->save();

		$speciesRestrictionArray = [];
		if (!empty($this->args["input"]["itemSpeciesRestrictions"])) {
			foreach (
				$this->args["input"]["itemSpeciesRestrictions"]
				as $itemSpeciesRestriction
			) {
				$speciesRestrictionArray[] = $itemSpeciesRestriction["id"];

				ItemSpeciesRestriction::on($this->subdomainName)->firstOrCreate(
					[
						"item_id" => $id,
						"species_id" => $itemSpeciesRestriction["id"],
					],
				);
			}
		}
		//remove items not included
		$modelInstance
			->findorFail($id)
			->itemSpeciesRestrictions->whereNotIn(
				"species_id",
				$speciesRestrictionArray,
			)
			->each->delete();

		$reminderReplacesArray = [];
		if (!empty($this->args["input"]["reminderReplaces"])) {
			foreach (
				$this->args["input"]["reminderReplaces"]
				as $reminderReplace
			) {
				$reminderReplacesArray[] = $reminderReplace["id"];

				ItemReminderReplaces::on($this->subdomainName)->firstOrCreate([
					"item_id" => $id,
					"replaces_item_id" => $reminderReplace["id"],
				]);
			}
		}
		//remove items not included
		$modelInstance
			->findorFail($id)
			->reminderReplaces->whereNotIn(
				"replaces_item_id",
				$reminderReplacesArray,
			)
			->each->delete();

		$locationsArray = [];
		if (!empty($this->args["input"]["itemLocations"])) {
			foreach ($this->args["input"]["itemLocations"] as $itemLocation) {
				$locationsArray[] = $itemLocation["id"];

				ItemLocation::on($this->subdomainName)->firstOrCreate([
					"item_id" => $id,
					"location_id" => $itemLocation["id"],
				]);
			}
		}

		//remove items not included
		$modelInstance
			->findorFail($id)
			->itemLocations->whereNotIn("location_id", $locationsArray)
			->each->delete();

		$itemTaxesArray = [];

		if (!empty($this->args["input"]["itemTaxes"])) {
			foreach ($this->args["input"]["itemTaxes"] as $itemTax) {
				$itemTaxesArray[] = $itemTax["id"];

				ItemTax::on($this->subdomainName)->firstOrCreate([
					"item_id" => $id,
					"tax_id" => $itemTax["id"],
				]);
			}
		}

		//remove items not included
		$modelInstance
			->findorFail($id)
			->itemTaxes->whereNotIn("tax_id", $itemTaxesArray)
			->each->delete();

		$arrayList = array_column(
			$this->args["input"]["itemVolumePricing"],
			"id",
		);
		//remove items not included
		$modelInstance
			->findorFail($id)
			->itemVolumePricing->whereNotIn("id", $arrayList)
			->each->delete();

		if (!empty($this->args["input"]["itemVolumePricing"])) {
			foreach (
				$this->args["input"]["itemVolumePricing"]
				as $key => $input
			) {
				ItemVolumePricing::on($this->subdomainName)->UpdateOrCreate(
					[
						"id" => $input["id"],
					],
					[
						"item_id" => $this->args["id"],
						"quantity" => $input["quantity"],
						"unit_price" => $input["unitPrice"],
					],
				);
			}
		}

		$arrayList = array_column($this->args["input"]["itemKitItems"], "id");
		//remove items not included
		$modelInstance
			->findorFail($id)
			->itemKitItems->whereNotIn("id", $arrayList)
			->each->delete();

		if (!empty($this->args["input"]["itemKitItems"])) {
			foreach ($this->args["input"]["itemKitItems"] as $key => $input) {
				ItemKitItem::on($this->subdomainName)->updateOrCreate(
					[
						"id" => $input["id"],
					],
					[
						"item_kit_id" => $this->args["id"],
						"item_id" => $input["item_id"],
						"quantity" => $input["quantity"],
					],
				);
			}
		}
		$primaryKey = $modelInstance->getPrimaryKey();

		$this->affectedId = $modelInstance->$primaryKey;

		return $this->model
			::on($this->subdomainName)
			->where($primaryKey, $modelInstance->$primaryKey)
			->paginate(1);
	}
}
