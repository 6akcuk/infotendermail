<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UsersRequest;
use App\Models\Authority\Role;
use App\Models\Geography\City;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsersController extends Controller
{

    public function __construct()
    {
        //$this->loadAndAuthorizeResource(['class' => App\Models\User::class]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->input('q')) {

        }
        $users = User::paginate(50);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(UsersRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        flash()->success('Пользователь добавлен.');

        return redirect()->route('admin.users.index');
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
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UsersRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $update = [
            'name' => $request->name,
            'email' => $request->email
        ];

        if ($request->password) {
            $update['password'] = bcrypt($request->password);
        }

        $user->update($update);

        flash()->success('Изменения сохранены.');

        return redirect()->route('admin.users.index', $user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if ($id == 1)
            abort('403', 'Нельзя удалить данного пользователя.');

        $user = User::findOrFail($id);
        $user->delete();

        flash()->success('Успешно удален.');

        return redirect()->route('admin.users.index');
    }
}
