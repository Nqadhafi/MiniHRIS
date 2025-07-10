<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $roles = DB::table('roles')->get();
        return view('dashboard.superadmin.role.index', compact('roles'));
    }

    public function create()
    {
        return view('dashboard.superadmin.role.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles',
        ]);

        DB::table('roles')->insert([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('settings.roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function show($id)
    {
        $role = DB::table('roles')->where('id', $id)->first();

        if (!$role) abort(404);

        return view('dashboard.superadmin.role.show', compact('role'));
    }

    public function edit($id)
    {
        $role = DB::table('roles')->where('id', $id)->first();

        if (!$role) abort(404);

        return view('dashboard.superadmin.role.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        DB::table('roles')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_at' => now()
            ]);

        return redirect()->route('settings.roles.index')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy($id)
    {
        DB::table('roles')->where('id', $id)->delete();
        return redirect()->route('settings.roles.index')->with('success', 'Role berhasil dihapus');
    }
}
