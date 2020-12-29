<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileWebController extends Controller
{
    public function index()
    {
        dd(Auth::user());
        $user = User::all();

        return view('profile.index', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'confirmed',
        ]);

        $user = User::where('id', $id)->first();
        $user->name = $user->name;
        $user->alamat = $user->almat;
        $user->foto = $user->foto;
        $user->phone = $user->phone;

        if (!empty($request->passwrod)) {
            $user->password = Hash::make($request->password);
        }

        $user->update();
        return redirect(route('profile.index'))->with(['Success' => 'Profile telah diperbarui']);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!empty($user)) {
            $user->delete();
            return redirect(route('profile.index'))->with(['Success' => 'Profile telah terhapus']);
        }
        return redirect(route('profile.index'))->with(['Failed' => 'Profile gagal terhapus']);
    }
}
