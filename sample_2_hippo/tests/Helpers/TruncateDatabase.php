<?php

namespace Tests\Helpers;

use App;
use DB;
use Eloquent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Schema;

trait TruncateDatabase
{
	use DatabaseTransactions;

	protected function truncateTestDatabase(): void
	{
		if (App::environment() === "production") {
			exit();
		}

		// Truncate all tables, except migrations
		DB::statement("SET foreign_key_checks=0");
		$tables = DB::select("SHOW TABLES");
		foreach ($tables as $table) {
			//TODO
			//Find better way to get table name hard code hippodb_test.... tired
			$name = $table->Tables_in_hippodb_test;

			$nonEraseArray = [
				//"invoices",
				"migrations",
				"account_categories",
				"chart_of_accounts",
				"inventory_statuses",
				"inventory_transaction_statuses",
				"invoice_statuses",
				//"item_categories",
				"item_types",
				//"items",
				"ospos_app_config",
				//"ospos_items",
				"ospos_modules",
				"ospos_stripe_platform_config",
				//"ospos_receivings",
				//"ospos_items",
				"payment_methods",
				"payment_platforms",
				"permissions",
				//"receiving_items",
				//"receivings",
				"receiving_statuses",
				"reminder_intervals",
				"role_has_permissions",
				"roles",
				"tblAccessLevels",
				//"tblAllergies",
				//"tblAppointmentStatuses",
				//"tblBreeds",
				"tblBulkPaymentMethods",
				"tblChartTemplatesSoap",
				"tblChartTypes",
				//"tblClients",
				//"tblColors",
				"tblCreditCardTypes",
				"tblCustomReportsAvailableFields",
				"tblDefaultItems",
				"tblDefaultSchedulerEvents",
				"tblDefaultSchedulerEventTypes",
				"tblDefaultSchedulerResources",
				"tblDefaultUserQuickLinks",
				"tblDefaultUserWidgets",
				"tblDegrees",
				"tblDrugAllergies",
				"tblEnrollPHRs",
				"tblFoldersTemplateChart",
				//"tblGenders",
				//"tblHydrationStatusOptions",
				"tblInsuranceProviders",
				"tblLogActions",
				"tblMarkings",
				//"tblMucousMembraneOptions",
				//"tblOrganizationLocations",
				//"tblOrganizations",
				"tblOrganizationSettings",
				"tblPatientAnimalBreeds",
				"tblPatientOwnerInformation", //maybe
				"tblPatientOwners",
				"tblPortalLogActions",
				"tblPreferredCommunications",
				"tblPrefixes",
				"tblQuickLinks",
				"tblRegions",
				"tblReminderRepeats",
				"tblRosKeywords",
				"tblSalesStatuses",
				//"tblSchedule",
				"tblSchedulerResources",
				"tblSchedulerEventTypes",
				"tblSchedulerRepeats",
				"tblSecretQuestions",
				"tblSpecialties",
				//"tblSpecies",
				"tblSubRegions",
				"tblSuffixes",
				"tblTemplatesChart",
				"tblTemplatesMedication",
				"tblTemplatesMedicationInventory",
				"tblTimezones",
				"tblTitles",
				"tblTreatmentSheetRejectReasons",
				"tblTreatmentSheetRemoveReasons",
				"tblUnits",
				"tblUserWidgets",
				"tblWidgets",
				"tblZipCodes",
				//"model_has_roles",
				"roles",
			];
			if (in_array($name, $nonEraseArray) || !Schema::hasTable($name)) {
				continue;
			}
			DB::table($name)->truncate();
		}
		usleep(250000);
		DB::statement("SET foreign_key_checks=1");
	}
}
