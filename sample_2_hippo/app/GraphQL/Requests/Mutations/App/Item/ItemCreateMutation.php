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
use Illuminate\Contracts\Auth\Authenticatable;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ItemCreateMutation extends AppHippoMutation
{
	protected $model = Item::class;

	protected $permissionName = "Items: Create";

	protected $attributes = [
		"name" => "ItemCreateMutation",
	];

	protected $actionId = HippoGraphQLActionCodes::ITEM_CREATE;

	public function args(): array
	{
		return [
			"unitPriceDisabled" => [
				"name" => "unitPriceDisabled",
				"type" => Type::boolean(),
			],
			"input" => [
				"type" => GraphQL::type("ItemCreateInput"),
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
		$modelInstance = new $this->model();
		$modelInstance->setConnection($this->subdomainName);

		$id = $modelInstance->create($this->args["input"])->id;

		if (!empty($this->args["input"]["itemSpeciesRestrictions"])) {
			foreach (
				$this->args["input"]["itemSpeciesRestrictions"]
				as $itemSpeciesRestriction
			) {
				ItemSpeciesRestriction::on($this->subdomainName)->create([
					"item_id" => $id,
					"species_id" => $itemSpeciesRestriction["id"],
				]);
			}
		}

		if (!empty($this->args["input"]["reminderReplaces"])) {
			foreach (
				$this->args["input"]["reminderReplaces"]
				as $reminderReplace
			) {
				ItemReminderReplaces::on($this->subdomainName)->create([
					"item_id" => $id,
					"replaces_item_id" => $reminderReplace["id"],
				]);
			}
		}

		if (!empty($this->args["input"]["itemLocations"])) {
			foreach ($this->args["input"]["itemLocations"] as $itemLocation) {
				ItemLocation::on($this->subdomainName)->create([
					"item_id" => $id,
					"location_id" => $itemLocation["id"],
				]);
			}
		}

		if (!empty($this->args["input"]["itemTaxes"])) {
			foreach ($this->args["input"]["itemTaxes"] as $itemTax) {
				ItemTax::on($this->subdomainName)->create([
					"item_id" => $id,
					"tax_id" => $itemTax["id"],
				]);
			}
		}

		if (!empty($this->args["input"]["itemVolumePricing"])) {
			foreach (
				$this->args["input"]["itemVolumePricing"]
				as $key => $input
			) {
				ItemVolumePricing::on($this->subdomainName)->create([
					"item_id" => $id,
					"quantity" => $input["quantity"],
					"unit_price" => $input["unitPrice"],
				]);
			}
		}

		if (!empty($this->args["input"]["itemKitItems"])) {
			foreach ($this->args["input"]["itemKitItems"] as $itemKitItems) {
				ItemKitItem::on($this->subdomainName)->create([
					"item_kit_id" => $id,
					"item_id" => $itemKitItems["item_id"],
					"quantity" => $itemKitItems["quantity"],
				]);
			}
		}
		$primaryKey = $modelInstance->getPrimaryKey();

		$this->affectedId = $id;

		return $this->model
			::on($this->subdomainName)
			->where($primaryKey, $id)
			->paginate(1);
	}
}
