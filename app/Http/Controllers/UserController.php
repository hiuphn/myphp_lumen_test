<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return response()->json(User::all());
    }

    public function addUser(Request $request){
        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function showUser($id){
        $user = User::find($id);
        if($user){
            return response()->json($user);
        }
        return response()->json(['Người dùng không tồn tại'], 404);
    }

    public function editUser(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['Khong tim thay nguoi dung'], 404);

        }
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroyUser($id){
        $user = User::find($id);
        if(!$user){
            return response()-> json(['Không tìm thấy người dùng để xóa'], 404);

        }
        $user->delete();
        return response()->json(['Người dùng đã được xóa thành công']);
    }
}
