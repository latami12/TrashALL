<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PengurusController extends Controller
{
    public function index()
    {
        return view('admin.create');
    }

    public function show()
    {
        $users = User::all();
    
        return view('admin.create', compact('users'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed',
            'role_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response($validator->errors());
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role_id' => $request->get('role_id')
        ]);

        // $token = JWTAuth::fromUser($user);

        $user->save();
        // $user = User::all();

        return redirect(route('admin.index'));
    }
}
