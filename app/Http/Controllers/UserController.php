<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|confirmed',
            'tc'=>'required'
        ]);

        if (User::where('email',$request->email)->first()) {
            return response([
                'message'=>'This email already registered',
                'status'=>'fail'
            ], 200);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->tc = json_decode($request->tc);
        $user->save();

        $token = $user->createToken($request->email)->plainTextToken;

        return response([
            'user'=>$user,
            'token'=>$token,
            'message'=>'Registration Successfully',
            'status'=>'success'
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $email = User::where('email', $request->email)->first();
        if(!$email || !Hash::check($request->password, $email->password)) {
            return response([
                'message'=>'Email id or Password are incorrect',
                'status'=>'failed'
            ], 400);
        }

        $token = $email->createToken($request->email)->plainTextToken;

        return response([
            'token'=>$token,
            'message'=>'Login Successfully',
            'status'=>'success'
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json([
            'message'=>'logout successfully',
            'status'=>'success'
        ], 200);
    }

    public function loggedUser(Request $request) {
        $user = $request->user();

        return response([
            'user'=>$user,
            'message'=>'logged user data',
            'status'=>'success'
        ], 200);
    }

    public function password_change(Request $request) {
        $request->validate([
            'password'=>'required|confirmed'
        ]);

        $pchange = $request->user();
        $pchange->password = Hash::make($request->password);
        $pchange->save();

        return response([
            'message'=>'Password changed successfully',
            'status'=>'success'
        ]);
    }
}
