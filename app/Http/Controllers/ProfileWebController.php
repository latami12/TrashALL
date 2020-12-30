<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
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
        $user->name = $request->name;
        $user->alamat = $request->almat;
        $user->phone = $request->phone;

        if (!empty($request->passwrod)) {
            $user->password = Hash::make($request->password);
        }

        if ($request->foto) {
            // $image = $request->image->getClientOriginalName() . '-' . time() . '.' . $request->image->extension();
            // $request->image->move(public_path('img'), $image);

            $img = base64_encode(file_get_contents($request->foto));
            $client = new Client();
            $res = $client->request('POST', 'https://freeimage.host/api/1/upload', [
                'form_params' => [
                    'key' => '6d207e02198a847aa98d0a2a901485a5',
                    'action' => 'upload',
                    'source' => $img,
                    'format' => 'json',
                ]
            ]);
            $array = json_decode($res->getBody()->getContents());
            // dd($array);
            $foto = $array->foto->file->resource->chain->foto;
            $user->foto = $foto;
            // dd($filename);
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
