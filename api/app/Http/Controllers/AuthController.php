<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $first = $errors->getMessages();
            return response()->json([
                'status'  => false,
                'message' => (reset($first))[0],
                'errors'  => $errors
            ]);
        }

        $data['password'] = Hash::make($request->password);

        $user = User::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Registered successfully!',
            'data'    => $user
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'The provided credentials are incorrect.'
            ]);
        }

        $token = $user->createToken('browser');

        return response()->json([
            'status' => true,
            "token"  => $token->plainTextToken,
            "data"   => $user
        ]);
    }

    public function refresh(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'token' => $request->user()->createToken('api')->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            "data"   => $request->user()
        ]);
    }
}
