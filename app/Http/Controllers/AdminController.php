<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

class AdminController extends Controller
{
    public function assignAdminRole(Request $request, $userId)
    {
        //Kiểm tra xem người thực hiện có phải là admin không
        $adminRole = Role::where('name', 'admin')->first();
        $currentUser = auth()->user();

        if (!$currentUser || !UserRole::where('user_id', $currentUser->id)->where('role_id', $adminRole->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Gán role admin cho user được chọn
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại '], 404);
        }

        UserRole::firstOrCreate([
            'user_id' => $user->id,
            'role_id' => 1,
        ]);

        return response()->json(['message' => 'Đã cập nhật thành Admin!']);
    }
}
