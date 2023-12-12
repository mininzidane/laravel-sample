<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\StorePermission;
use App\Http\Requests\Permission\UpdatePermission;
use App\Models\Authorization\Permission;

class PermissionController extends Controller
{
	public function __construct()
	{
	}

	public function index()
	{
		$permissions = Permission::all();

		return response($permissions);
	}

	public function store(StorePermission $request)
	{
		$permission = new Permission([
			"name" => $request->get("name"),
			"guard_name" => $request->get("guard"),
		]);

		$permission->save();

		return response($permission);
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update(UpdatePermission $request, $id)
	{
		$permission = Permission::findOrFail($id);

		if ($request->has("name")) {
			$permission->name = $request->get("name");
		}

		if ($request->has("guard")) {
			$permission->guard_name = $request->get("guard");
		}

		$permission->save();
	}

	public function destroy($id)
	{
		return response(Permission::destroy($id));
	}
}
