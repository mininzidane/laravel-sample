<?php

namespace App\Reports;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ReportModel extends Model
{
	protected const FORMAT_PDF = "pdf";

	/**
	 * @var mixed|string
	 */
	protected $salesType;

	/**
	 * @var mixed|string
	 */
	protected $salesStatus;

	/**
	 * @var mixed|string
	 */
	protected $locationPredicate;
	/**
	 * @var mixed
	 */
	protected $queryParameters;

	protected string $format;

	/**
	 * @param array $parametersArray
	 * @return $this
	 */
	public function setQueryParameters($parametersArray): ReportModel
	{
		$this->queryParameters = $parametersArray;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getQueryParameters(): array
	{
		return $this->queryParameters;
	}

	/**
	 * @param Request $request
	 * @return ReportModel
	 * sales (use >) or returns (use <)
	 * sales = 1 , returns = 2
	 */
	public function setSaleTypeSql(Request $request): ReportModel
	{
		if ($request->input("saleType") == "1") {
			$this->salesType = ">";
		} else {
			$this->salesType = "<";
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSalesTypeSql(): string
	{
		return $this->salesType;
	}

	/**
	 * @param Request $request
	 * @return ReportModel
	 * 1 = open sales choose sale_time column otherwise choose sale_completed_time
	 * TODO what about estimates?
	 */
	public function setSalesStatusSql(Request $request): ReportModel
	{
		if ($request->input("saleStatus") == "2") {
			$this->salesStatus = "completed_at";
		} else {
			$this->salesStatus = "created_at";
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSalesStatusSql(): string
	{
		return $this->salesStatus;
	}

	public function setReplicaConnection(Request $request)
	{
		$this->setConnection("replica_" . $request->header("Subdomain"));
	}

	/**
	 * @param Request $request
	 * @return ReportModel
	 * App excludeReminders values: 0 == exclude, 1 == include
	 */
	public function setLocationPredicate(Request $request): ReportModel
	{
		$locations = implode(",", $request->input("locations"));

		if ($request->input("excludeReminders")) {
			$this->locationPredicate = " (tblClientReminders.location_id is null OR FIND_IN_SET(tbl_reminder_location.id, '$locations')) ";
		} else {
			$this->locationPredicate = " FIND_IN_SET(tbl_reminder_location.id, '$locations') ";
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocationPredicate(): string
	{
		return $this->locationPredicate;
	}

	public function generateReport(Request $request)
	{
		// The replica connection has to be set here since this object's constructor fires off before the middleware.
		$this->setReplicaConnection($request);
		return $this->generateReportData();
	}

	public function setFormat(Request $request): self
	{
		$this->format = $request->input("format") ?? self::FORMAT_PDF;
		return $this;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public function generateReportData()
	{
		throw new Exception(
			"Generate Report Data function was not implemented.",
		);
	}
}
