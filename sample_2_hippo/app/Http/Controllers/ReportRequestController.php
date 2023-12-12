<?php

namespace App\Http\Controllers;

use App\Models\ReportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportRequestController extends Controller
{
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse
	{
		$subdomain = $request->header("Subdomain");

		if (empty($subdomain)) {
			return response()->json(
				"A subdomain must be specified in the request",
				400,
			);
		}

		$validator = Validator::make($request->all(), [
			"name" => "required|string",
			"user_id" => "required|numeric",
			"file_name" => "required|string",
			"format" => "required|string",
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors()->all(), 400);
		}

		$reportRequest = ReportRequest::on($subdomain)->create([
			"name" =>
				str_replace(" ", "_", $validator->getData()["name"]) .
				"_" .
				date("Y-m-d_H-i-s", time()),
			"user_id" => intval($validator->getData()["user_id"]),
			"is_ready" => false,
			"file_name" => $validator->getData()["file_name"],
			"format" => $validator->getData()["format"],
		]);

		$reportRequest->save();

		return response()->json($reportRequest);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param $id
	 * @return JsonResponse
	 */
	public function show(Request $request, $id): JsonResponse
	{
		$subdomain = $request->header("Subdomain");

		if (empty($subdomain)) {
			return response()->json(
				"A subdomain must be specified in the request",
				400,
			);
		}

		return response()->json(ReportRequest::on($subdomain)->findOrFail($id));
	}

	/**
	 * Update the specified resource in storage, only for the is_ready field.
	 *
	 * @param Request $request
	 * @param int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $id): JsonResponse
	{
		$subdomain = $request->header("Subdomain");

		if (empty($subdomain)) {
			return response()->json(
				"A subdomain must be specified in the request",
				400,
			);
		}

		$validator = Validator::make($request->all(), [
			"is_ready" => "required|boolean",
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors()->all(), 400);
		}

		$reportRequest = ReportRequest::on($subdomain)
			->where("id", "=", $id)
			->firstOrFail();
		$reportRequest->is_ready = $validator->getData()["is_ready"];
		$reportRequest->save();

		return response()->json($reportRequest);
	}
}
