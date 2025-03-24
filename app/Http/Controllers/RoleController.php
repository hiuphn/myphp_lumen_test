<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    // Lấy danh sách vai trò
    public function index()
    {
        return response()->json(Role::all());
    }

    // Tạo mới vai trò
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles',
            'description' => 'nullable|string'
        ]);

        $role = Role::create($request->only(['name', 'description']));

        return response()->json($role, 201);
    }

    // Lấy thông tin vai trò theo ID
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }
        return response()->json($role);
    }

    // Cập nhật vai trò
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $this->validate($request, [
            'name' => 'sometimes|unique:roles,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $role->update($request->only(['name', 'description']));
        return response()->json($role);
    }

    // Xóa vai trò
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }
}
