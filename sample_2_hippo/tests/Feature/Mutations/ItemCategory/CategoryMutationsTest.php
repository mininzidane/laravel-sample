<?php

namespace Tests\Feature\Mutations\ItemCategory;

use App\Models\Item;
use App\Models\ItemCategory;
use Tests\Helpers\TruncateDatabase;
use Tests\Helpers\PassportSetupTestCase;

class CategoryMutationsTest extends PassportSetupTestCase
{
	use TruncateDatabase;

	public function test_can_create_category()
	{
		$response = $this->postGraphqlJson(
			$this->buildQuery("", "Test Category"),
		);

		$response->assertJsonStructure([
			"data" => [
				"itemCategoryCreate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$idCreated = $response->json("data.itemCategoryCreate.data.*.id")[0];

		$response->assertExactJson([
			"data" => [
				"itemCategoryCreate" => [
					"data" => [
						[
							"id" => $idCreated,
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("item_categories", [
			"id" => $idCreated,
			"name" => "Test Category",
		]);
	}

	public function test_category_cannot_be_created_without_name()
	{
		$response = $this->postGraphqlJson($this->buildQuery("", ""));

		$this->assertContains(
			"The value must not be blank",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_category_name_must_be_unique()
	{
		$this->postGraphqlJson($this->buildQuery("", "Test Category"));

		$response = $this->postGraphqlJson(
			$this->buildQuery("", "Test Category"),
		);

		$this->assertContains(
			"The name must be unique",
			$response->json("*.*.extensions.validation.*.*"),
		);
	}

	public function test_can_update_category()
	{
		$category = ItemCategory::factory()->create();

		$query =
			'mutation {
			itemCategoryUpdate (id: "' .
			$category->id .
			'", input: {
				id: "' .
			$category->id .
			'",
				name: "Updated Category"
			}) {
				data {
					id
				}
			}
		}';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"itemCategoryUpdate" => [
					"data" => [
						"*" => ["id"],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"itemCategoryUpdate" => [
					"data" => [
						[
							"id" => "$category->id",
						],
					],
				],
			],
		]);

		$this->assertDatabaseHas("item_categories", [
			"id" => 1,
			"name" => "Updated Category",
		]);
	}

	/*
	 * Protection against category deletion with associated items
	 * is done by App checking relationshipNumber returned by API
	 */
	public function test_cannot_delete_category_if_items_are_assigned()
	{
		$category = ItemCategory::factory()->create();
		Item::factory()->create(["category_id" => $category->id]);

		$query =
			'{      
			itemCategories(id: ' .
			$category->id .
			') {
				data {
			       	relationshipNumber
			    }     
			}    
		}';

		$response = $this->postGraphqlJson($query);

		$response->assertExactJson([
			"data" => [
				"itemCategories" => [
					"data" => [
						0 => [
							"relationshipNumber" => "1",
						],
					],
				],
			],
		]);
	}

	public function test_can_delete_unassociated_category()
	{
		$category = ItemCategory::factory()->create();

		$query =
			'
			mutation {
		    	itemCategoryDelete(id: "' .
			$category->id .
			'") {
					data {
						id
					}
				}
			}';

		$response = $this->postGraphqlJson($query);

		$response->assertStatus(200);
		$this->assertSoftDeleted("item_categories", ["id" => $category->id]);
	}

	/*
	 * Returns the exact graphQL create mutation query from the APP. Since it does not
	 * utilize input variables, we'll build the query here
	 */
	private function buildQuery(string $id, string $name): string
	{
		return '
			mutation {
				itemCategoryCreate (input: {
					id: "' .
			$id .
			'"  
					name: "' .
			$name .
			'"		
				}) {
					data {
				  	id
				}
			  }
			}';
	}
}
