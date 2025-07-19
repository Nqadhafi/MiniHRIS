<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->get();

        return view('dashboard.superadmin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = DB::table('roles')->get();
        return view('dashboard.superadmin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        DB::table('users')->insert([
            'username' => strtolower(str_replace(' ', '', $request->username)),
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('settings.users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show($id)
    {
        $user = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->where('users.id', $id)
            ->first();

        if (!$user) abort(404);

        return view('dashboard.superadmin.user.show', compact('user'));
    }

    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        $roles = DB::table('roles')->get();

        if (!$user) abort(404);

        return view('dashboard.superadmin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'password' => 'nullable|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'username' => strtolower(str_replace(' ', '', $request->username)),
            'role_id' => $request->role_id,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($data);

        return redirect()->route('settings.users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        return back()->with('success', 'User berhasil dihapus');
    }
}
