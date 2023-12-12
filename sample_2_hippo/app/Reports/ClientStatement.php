<?php

namespace App\Reports;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Invoice;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;

class ClientStatement extends ReportModel
{
	public function __construct(Request $request)
	{
		$this->setQueryParameters([
			"location" => $request->input("locations")[0],
			"ownerId" => $request->input("ownerId"),
			"patientId" => $request->input("patientId"),
		]);

		parent::__construct();
	}

	public function generateReportData(): JsonResponse
	{
		$invoices = Invoice::on($this->getConnectionName())
			->where("owner_id", $this->getQueryParameters()["ownerId"])
			->where("location_id", $this->getQueryParameters()["location"])
			->where("status_id", 1)
			->where("total", ">", 0.0)
			->with([
				"user",
				"patient:id,first_name",
				"invoiceItems",
				"invoiceItems.invoiceItemTaxes",
				"invoicePayments" => function ($query) {
					$query->whereNotNull("amount_applied");
				},
				"invoicePayments.payment",
				"invoicePayments.payment.paymentMethod",
			])
			->get()
			->map(function ($item) {
				return collect($item)->except([
					"patient.primaryOwner",
					"patient.owners",
				]);
			});

		$owner = Owner::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["ownerId"])
			->with("subregion")
			->first();

		$otherOwners = DB::connection($this->getConnectionName())->select(
			DB::raw(
				"
				SELECT
				other_owner_info.client_id,
				other_owner_info.first_name,
				other_owner_info.last_name,
				other_owner_info.address1,
				other_owner_info.address2,
				other_owner_info.city,
				other_owner_info.zip,
				other_owner_info.country,
				other_owner_info.primary,
				states.code AS state,
				patient.deleted_at
			FROM
				tblPatientOwners AS patient
				INNER JOIN tblPatientOwnerInformation AS other_owner_info ON patient.owner_id = other_owner_info.id
				INNER JOIN tblSubRegions AS states ON other_owner_info.state = states.id
			WHERE
				patient.client_id = " .
					$this->getQueryParameters()["patientId"] .
					"
				AND patient.primary = 0
				AND patient.deleted_at IS NULL
				AND patient.relationship_type = 'Owner'
			",
			),
			$this->getQueryParameters(),
		);

		$location = Location::on($this->getConnectionName())
			->where("id", $this->getQueryParameters()["location"])
			->with("subregion", "organization", "tz")
			->first();

		return response()->json([
			"invoices" => $invoices,
			"owner" => $owner,
			"otherOwners" => $otherOwners,
			"location" => $location,
			"credits" => $owner->credits->sum("value"),
		]);
	}
}
