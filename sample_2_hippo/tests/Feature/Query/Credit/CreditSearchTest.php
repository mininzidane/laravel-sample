<?php

namespace Tests\Feature\Query\Credit;

use Illuminate\Support\Carbon;
use Tests\Helpers\MutationTestHelpers;
use Tests\Helpers\PassportSetupTestCase;
use Tests\Helpers\TruncateDatabase;

class CreditSearchTest extends PassportSetupTestCase
{
	use TruncateDatabase, MutationTestHelpers;

	public function test_credit_search_does_not_find_first_credit_with_blank()
	{
		$this->gift_card_create_without_number();
		$this->gift_card_create_with_number();

		$query = '{
                    credits(number: "", type: "Gift Card", exactMatch: true) {
                        data {
                            id
                            number
                            type
                            currentValue
                            originalValue
                            updatedAt
                            owner {
                                firstName
                                lastName
                                fullName
                            }
                        }
                    }
                }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"credits" => [
					"data" => [
						"*" => [],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"credits" => [
					"data" => [],
				],
			],
		]);
	}

	public function test_credit_search_find_credit_with_exact_match()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);
		$this->gift_card_create_without_number();
		$this->gift_card_create_with_number();

		$query = '{
                    credits(number: "1006ed46-9aa6-49c3-99ea-ec64cb0ff817", type: "Gift Card", exactMatch: true) {
                        data {
                            id
                            number
                            type
                            currentValue
                            originalValue
                            updatedAt
                            owner {
                                firstName
                                lastName
                                fullName
                            }
                        }
                    }
                }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonStructure([
			"data" => [
				"credits" => [
					"data" => [
						"*" => [
							"currentValue",
							"id",
							"number",
							"originalValue",
							"type",
							"updatedAt",
							"owner",
						],
					],
				],
			],
		]);

		$response->assertExactJson([
			"data" => [
				"credits" => [
					"data" => [
						[
							"currentValue" => 777,
							"id" => "2",
							"number" => "1006ed46-9aa6-49c3-99ea-ec64cb0ff817",
							"originalValue" => 777,
							"owner" => [
								"firstName" => "",
								"fullName" => " ",
								"lastName" => "",
							],
							"type" => "Gift Card",
							"updatedAt" => "2022-05-21 12:00:00",
						],
					],
				],
			],
		]);
	}

	public function test_credit_search_find_all_credits()
	{
		$now = Carbon::create(2022, 5, 21, 12);
		Carbon::setTestNow($now);

		$this->gift_card_create_without_number();
		$this->gift_card_create_with_number();

		$query = '{
                    credits(number: "", type: "Gift Card") {
                        data {
                            id
                            number
                            type
                            currentValue
                            originalValue
                            updatedAt
                            owner {
                                firstName
                                lastName
                                fullName
                            }
                        }
                    }
                }
      ';

		$response = $this->postGraphqlJson($query);

		$response->assertJsonCount(2, "data.credits.data");
	}
}
