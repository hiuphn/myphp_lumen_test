<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller{
    public function register(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
    
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $userRole->id,
            ]);
        }
        return response()->json(['message' => 'Đăng kí thành công'], 201);
    }
    public function login(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        
        $credentials = $request->only('email', 'password');
        
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user || !$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Thông tin đăng nhập không chính xác'], 401);
        }
        // Lấy thông tin người dùng đã đăng nhập
        $user = Auth::user();

        // Tạo token với payload bao gồm customer_id
        $payload = [
            'customer_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ];

        $Token = Auth::claims($payload)->fromUser($user);

        return response()->json([
            'token' => $Token,
            'message' => 'Đăng nhập thành công',
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'customer_id' => $user->id  
        ], 200);
    }
    public function me(){
        return response()->json(auth()->user());
    }
    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }
}