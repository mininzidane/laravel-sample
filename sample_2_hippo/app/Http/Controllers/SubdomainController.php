<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subdomain\StoreSubdomain;
use App\Http\Requests\Subdomain\UpdateSubdomain;
use App\Models\Authorization\Permission;
use App\Models\Authorization\Subdomain;

class SubdomainController extends Controller
{
	public function __construct()
	{
	}

	public function index()
	{
		$subdomains = Subdomain::all();

		return response($subdomains);
	}

	public function connections()
	{
		return response(array_keys(Config("database.connections")));
	}

	public function store(StoreSubdomain $request)
	{
		$subdomain = new Subdomain([
			"name" => $request->get("name"),
			"active" => $request->get("active"),
		]);

		$subdomain->save();

		$permissionName = "GraphQL: Access " . $subdomain->name;

		$permission = Permission::findOrCreate($permissionName, "api");
		$permission->save();

		$subdomain->permission()->associate($permission);
		$subdomain->save();

		return response($subdomain);
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update(UpdateSubdomain $request, $id)
	{
		$subdomain = Subdomain::findOrFail($id);

		if ($request->has("name")) {
			$subdomain->name = $request->get("name");
		}

		if ($request->has("connection")) {
			$subdomain->connection = $request->get("connection");
		}

		if ($request->has("active")) {
			$subdomain->active = $request->get("active");
		}

		$subdomain->save();

		return response($subdomain);
	}

	public function destroy($id)
	{
		$subdomain = Subdomain::findOrFail($id);

		$permissionName = "GraphQL: Access " . $subdomain->name;

		$permission = Permission::findByName($permissionName, "api");

		if ($permission) {
			Permission::destroy($permission->id);
		}

		return response(Subdomain::destroy($id));
	}
}
