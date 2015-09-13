<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Authority\Role;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class RolesController extends Controller {

    public function __construct() {
        $this->loadAndAuthorizeResource(['class' => 'App\Models\Authority\Role']);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$roles = Role::all();
		$rolesTotal = Role::count();

		return view('admin.roles.index', compact('roles', 'rolesTotal'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.roles.create');
	}

  /**
   * Store a newly created resource in storage.
   *
   * @param CreateRoleRequest $request
   * @return Response
   */
	public function store(RoleRequest $request)
	{
		Role::create($request->all());
		Flash::success('Роль успешно создана.');
		return redirect()->route('admin.roles.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
	}

  /**
   * Update the specified resource in storage.
   *
   * @param  int $id
   * @param UpdateRoleRequest $request
   * @return Response
   */
	public function update($id, RoleRequest $request)
	{
		$role = Role::findOrFail($id);
        $role->update($request->all());

        Flash::success('Изменения успешно сохранены.');
        return redirect()->route('admin.roles.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$role = Role::findOrFail($id);
        $role->delete();

        Flash::success('Роль успешно удалена.');
        return redirect()->route('admin.roles.index');
	}

}
