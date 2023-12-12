<?php

namespace Tests\Feature\Introspections;

use Tests\TestCase;

class ItemLegacyDeprecationTest extends TestCase
{
	public function testItemsLegacyIsDeprecated()
	{
		// GraphQL's introspection query to get the schema
		$response = $this->postJson("/graphql/app", [
			"query" => '
                query {
                    __schema {
                        types {
                            name
                            fields(includeDeprecated: true) {
                                name
                                isDeprecated
                            }
                        }
                    }
                }
            ',
		]);

		$response->assertStatus(200);

		$types = $response->json("data.__schema.types");

		// Fields to skip because the are embedded in the model and are not accessible in the fields
		$skipFields = ["createdAt", "updatedAt", "deletedAt"];

		// Find the 'ItemLegacy' type
		foreach ($types as $type) {
			if ($type["name"] === "ItemLegacy") {
				// Check all fields
				foreach ($type["fields"] as $field) {
					// If the field is one of the fields to skip, continue to the next field
					if (in_array($field["name"], $skipFields)) {
						continue;
					}
					// Check if the field is not deprecated
					if ($field["isDeprecated"] === false) {
						// Fail the test if any of the fields are not deprecated
						$this->fail(
							"The '{$field["name"]}' field of 'ItemLegacy' type is not deprecated.",
						);
					}
				}

				// If all non-skipped fields are deprecated, pass the test
				$this->assertTrue(true);
				return;
			}
		}

		// Fail the test if 'ItemLegacy' type or 'isDeprecated' field not found
		$this->fail(
			"The 'ItemLegacy' type or 'isDeprecated' field not found in the schema.",
		);
	}
}
