<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\StoreRole;
use App\Http\Requests\Role\UpdateRole;
use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;

class RoleController extends Controller
{
	public function __construct()
	{
	}

	public function index()
	{
		$roles = Role::with("permissions")->get();

		return response($roles);
	}

	public function store(StoreRole $request)
	{
		$role = new Role([
			"name" => $request->get("name"),
			"guard_name" => $request->get("guard"),
		]);

		$role->save();

		return response($role);
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update(UpdateRole $request, $id)
	{
		$role = Role::findOrFail($id);

		if ($request->has("name")) {
			$role->name = $request->get("name");
		}

		if ($request->has("guard")) {
			$role->guard_name = $request->get("guard");
		}

		$role->save();

		if ($request->has("selectedPermissions")) {
			$permissions = Permission::find(
				$request->get("selectedPermissions"),
			);
			$role->syncPermissions($permissions);
		}
	}

	public function destroy($id)
	{
		return response(Role::destroy($id));
	}
}
