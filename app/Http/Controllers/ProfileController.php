<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index() 
    {
        $user = User::where('id', Auth::user()->id)->first();

        return $this->sendResponse('Success', 'Profile sukses terdata', $user, 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => 'confirmed',
        ]);

        $user = User::where('id', Auth::user()->id)->first();
        $user->name = $request->name;
        $user->alamat = $request->alamat;
        $user->foto = $request->foto;
        $user->phone = $request->phone;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->update();
        return $this->sendResponse('Success', 'Profile telah terupdate', $user, 200);
    }
}
