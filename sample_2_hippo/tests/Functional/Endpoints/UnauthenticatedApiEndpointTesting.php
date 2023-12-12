<?php

namespace Tests\Functional\Endpoints;

use App\Models\User;
use Tests\Helpers\TruncateDatabase;
use Tests\TestCase;

class UnauthenticatedApiEndpointTesting extends TestCase
{
	//By using Test case we bypass any auth.  This would like a user hitting our endpoints from the outside world
	use TruncateDatabase;

	public function getHeaders(): array
	{
		return [
			"subdomain" => "test",
		];
	}

	public function getSubdomain(): string
	{
		return $this->getSubdomain();
	}

	//Unauthenticated test for the Auth Api endpoints will result
	//in a 302.  Redirect to the welcome page
	public function test_lab_order_code_route_is_unauthenticated()
	{
		$response = $this->post("/api/lab/orderCode/test");
		$response->assertStatus(302);
	}

	public function test_zoetis_order_code_route_is_unauthenticated()
	{
		$response = $this->post("/api/lab/ZoetisOrderCode");
		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_users_index()
	{
		$this->get("/api/users")->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_roles_index()
	{
		$this->get("/api/roles")->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_permissions_index()
	{
		$this->get("/api/permissions")->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_subdomains_index()
	{
		$this->get("/api/subdomains")->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_lab_requisition_waiting()
	{
		$this->get("/api/labRequisitions/waiting")->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_activeIntegrations()
	{
		$this->get("/api/activeIntegrations")->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_requisitions_location()
	{
		$subdomain = $this->getSubdomain();
		$locationId = 1;
		$integration = "some_integration";

		$response = $this->get(
			"/api/$subdomain/location/$locationId/requisitions/$integration",
		);

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_requisitions_by_id()
	{
		$subdomain = $this->getSubdomain();
		$id = 1;

		$response = $this->post("/api/$subdomain/requisition/$id");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_requisitions_by_labs()
	{
		$subdomain = $this->getSubdomain();
		$type = "zoetis";

		$response = $this->post("/api/$subdomain/labs/$type");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_location_id_settings()
	{
		$subdomain = $this->getSubdomain();
		$id = 1;

		$response = $this->get("/api/$subdomain/location/$id/settings");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_invoices_purge()
	{
		$subdomain = $this->getSubdomain();

		$response = $this->post("/api/$subdomain/invoices/purge");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_users_revoked()
	{
		$response = $this->get("/api/user");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_my_permissions()
	{
		$response = $this->get("/api/users/my/permissions");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_can_access_version()
	{
		$response = $this->get("/api/version");

		$response->assertStatus(200);
	}

	//"middleware" => ["api.subdomain"] begins here
	public function test_unauthenticated_users_can_access_reset()
	{
		$response = $this->get("/api/subdomain/reset");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_verify()
	{
		$response = $this->get("/api/subdomain/verify");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_login()
	{
		$response = $this->get("/api/subdomain/login");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_logout()
	{
		$response = $this->get("/api/subdomain/logout");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_me()
	{
		$response = $this->get("/api/subdomain/me");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_zendLogin()
	{
		$response = $this->get("/api/zendLogin");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_zendAuth()
	{
		$response = $this->get("/api/zendAuth");

		$response->assertStatus(200);
	}

	public function test_unauthenticated_users_can_access_patients()
	{
		$response = $this->get("/api/patients");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_can_access_patients_search()
	{
		$response = $this->get("/api/patients/search");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_sendPasswordReset_userId()
	{
		$userId = 1;

		$response = $this->get("/api/sendPasswordReset/$userId");

		$response->assertStatus(302);
	}

	public function test_unauthenticated_users_cannot_access_sendUserVerification_userId()
	{
		$userId = 1;

		$response = $this->get("/api/sendUserVerification/$userId");

		$response->assertStatus(302);
	}

	//Reports
	//TODO: The next three need some work
	//store
	public function test_unauthenticated_users_cannot_access_reports_store()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		User::factory()
			->connection("hippodb_test")
			->create();
		$data = [
			"name" => "Test Report",
			"user_id" => 1,
			"file_name" => "TestString",
			"format" => "pdf",
		];

		$response = $this->postJson(
			"/api/report-requests",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	//only returns a 404 because of no report right now.  needs to authenticate
	//show
	public function test_unauthenticated_users_cannot_access_reports_show()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$id = "12345asdf";

		$response = $this->get("/api/report-requests/$id", $this->getHeaders());

		$response->assertStatus(302);
	}

	//update
	//only returns a 404 because of no report right now.  needs to authenticate
	public function test_unauthenticated_users_cannot_access_reports_update()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$id = "12345";
		$data = ["is_ready" => true];

		$response = $this->put(
			"/api/report-requests/$id",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessSummaryCustomers()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate1" => "2022-01-01",
			"beginDate2" => "2022-01-01",
			"endDate1" => "2022-01-31",
			"endDate2" => "2022-01-31",
			"saleStatus" => "active",
			"timeZone1" => "UTC-5",
			"timeZone2" => "UTC-5",
		];

		$response = $this->post(
			"/api/summaryCustomers",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessSummaryEmployeesController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate1" => "2022-01-01",
			"beginDate2" => "2022-01-01",
			"endDate1" => "2022-01-31",
			"endDate2" => "2022-01-31",
			"saleStatus" => "active",
			"timeZone1" => "UTC-5",
			"timeZone2" => "UTC-5",
		];

		$response = $this->post(
			"/api/summaryEmployees",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessSummaryItemsController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
			"saleStatus" => "active",
		];

		$response = $this->post(
			"/api/summaryItems",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessSummarySalesController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate1" => "2022-01-01",
			"beginDate2" => "2022-01-01",
			"endDate1" => "2022-01-31",
			"endDate2" => "2022-01-31",
			"saleStatus" => "active",
			"timeZone1" => "UTC-5",
			"timeZone2" => "UTC-5",
			"timeZone3" => "UTC-5",
			"timeZone4" => "UTC-5",
		];

		$response = $this->post(
			"/api/summarySales",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessSummarySuppliesController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
			"saleStatus" => "active",
		];

		$response = $this->post(
			"/api/summarySupplies",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessSummaryReceivingsController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
		];

		$response = $this->post(
			"/api/summaryReceivings",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedReceivingController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
		];

		$response = $this->post(
			"/api/detailedReceiving",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedSaleController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate1" => "2022-01-01",
			"beginDate2" => "2022-01-01",
			"endDate1" => "2022-01-31",
			"endDate2" => "2022-01-31",
			"saleStatus" => "active",
			"timeZone1" => "UTC-5",
			"timeZone2" => "UTC-5",
			"timeZone3" => "UTC-5",
			"timeZone4" => "UTC-5",
			"timeZone5" => "UTC-5",
		];

		$response = $this->post(
			"/api/detailedSale",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedPaymentsController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
			"saleStatus" => "active",
			"timeZone1" => "UTC-5",
			"timeZone2" => "UTC-5",
		];

		$response = $this->post(
			"/api/detailedPayments",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedSaleItemsController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
			"saleStatus" => "active",
			"timeZone" => "UTC-5",
		];

		$response = $this->post(
			"/api/detailedSaleItems",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessVaccineCertificateController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"detailId" => "1",
		];

		$response = $this->post(
			"/api/vaccineCertificate",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessVaccineHistoryController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"patientId" => "1",
			"locationId" => "1",
		];

		$response = $this->post(
			"/api/vaccineHistory",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDepositSlipController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
		];

		$response = $this->post("/api/depositSlip", $data, $this->getHeaders());

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessVcpReportController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"id" => "1",
		];
		//TODO:
		//MOCK OUT SET CONTRACT
		//WE NEED TO MOCK THIS OUT AS NOT TO HIT THE VCP ENDPOINT
		//$response = $this->post("/api/vcpReport", $data, $this->getHeaders());

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedClientStatementController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"ownerId" => "1",
		];

		$response = $this->post(
			"/api/clientStatement",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedClientStatementsController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
		];

		$response = $this->post(
			"/api/clientStatements",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessDetailedCategoryListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
		];

		$response = $this->post(
			"/api/categoryList",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessCondolencesListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
		];

		$response = $this->post(
			"/api/condolencesList",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessAnniversaryListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
		];

		$response = $this->post(
			"/api/anniversaryList",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessItemKitListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
		];

		$response = $this->post("/api/itemKitList", $data, $this->getHeaders());

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessInventoryReorderListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$response = $this->post(
			"/api/inventoryReorderList",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessInventoryExpirationListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
		];

		$response = $this->post(
			"/api/inventoryExpirationList",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessControlledSubstanceLogController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
		];

		$response = $this->post(
			"/api/controlledSubstanceLog",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessInventorySummaryController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
		];

		$response = $this->post(
			"/api/inventorySummary",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessItemHistoryController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"locations" => ["1"],
			"beginDate" => "2022-01-01",
			"endDate" => "2022-01-31",
			"detailId" => "1",
		];

		$response = $this->post("/api/itemHistory", $data, $this->getHeaders());

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessProviderProductionSummaryController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"location" => ["1"],
			"beginDate1" => "2022-01-01",
			"beginDate2" => "2022-01-01",
			"endDate1" => "2022-01-31",
			"endDate2" => "2022-01-31",
			"timeZone1" => "UTC-5",
			"timeZone2" => "UTC-5",
		];

		$response = $this->post(
			"/api/providerProductionSummary",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}

	public function testUnauthenticatedUsersCanAccessRemindersListController()
	{
		$this->markTestSkipped(
			"Must be revisited. After Authentication is fixed",
		);
		$data = [
			"location1" => ["1"],
			"location2" => ["1"],
			"beginDate1" => "2022-01-01",
			"beginDate2" => "2022-01-01",
			"endDate1" => "2022-01-31",
			"endDate2" => "2022-01-31",
			"beginDate3" => "2022-01-01",
			"endDate4" => "2022-01-31",
			"beginDate4" => "2022-01-01",
			"endDate3" => "2022-01-31",
			"include" => "true",
		];

		$response = $this->post(
			"/api/remindersList",
			$data,
			$this->getHeaders(),
		);

		$response->assertStatus(302);
	}
}
