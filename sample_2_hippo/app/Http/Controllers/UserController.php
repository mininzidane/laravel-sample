<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUser;
use App\Http\Requests\User\UpdateUser;
use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
	public function __construct()
	{
	}

	public function index()
	{
		$users = User::with(["roles", "permissions", "tokens"])->get();

		return response($users);
	}

	public function store(StoreUser $request)
	{
		$user = new User([
			"email" => $request->get("email"),
			"name" => $request->get("name"),
			"password" => Hash::make($request->get("password")),
		]);

		$user->save();

		return response($user);
	}

	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		//
	}

	public function update(UpdateUser $request, $id)
	{
		$user = User::findOrFail($id);

		if ($request->has("name")) {
			$user->name = $request->get("name");
		}

		if ($request->has("email")) {
			$user->email = $request->get("email");
		}

		if ($request->has("password")) {
			$user->password = Hash::make($request->get("password"));
		}

		if ($request->has("allowedApiTokenCount")) {
			$user->allowed_api_token_count = $request->get(
				"allowedApiTokenCount",
			);
		}

		$user->save();

		if ($request->has("selectedRoles")) {
			$roles = Role::find($request->get("selectedRoles"));

			$user->syncRoles($roles);
		}

		if ($request->has("selectedPermissions")) {
			$permissions = Permission::find(
				$request->get("selectedPermissions"),
			);

			$user->syncPermissions($permissions);
		}
	}

	public function destroy($id)
	{
		return response(User::destroy($id));
	}

	public function permissions()
	{
		$permissions = Auth::user()->permissions;

		$permissionNames = array_map(function ($permission) {
			return $permission["name"];
		}, $permissions->toArray());

		return $permissionNames;
	}
}
