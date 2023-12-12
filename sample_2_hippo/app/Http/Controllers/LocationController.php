<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationSetting;

class LocationController extends Controller
{
	public function __construct()
	{
		//
	}

	public function index()
	{
		$locations = Location::all();

		return response($locations);
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update($request, $id)
	{
		//
	}

	public function destroy($id)
	{
		//
	}

	public function settings($subdomainName, $id)
	{
		$this->createSubdomainConnection($subdomainName);

		$settings = LocationSetting::on($subdomainName)
			->where("location_id", "=", $id)
			->get();

		return response($settings);
	}
}
