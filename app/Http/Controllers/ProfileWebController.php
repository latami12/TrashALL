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
        $users = User::all();

        return view('admin.users', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'confirmed',
        ]);

        $user = User::where('id', $id)->first();
        $user->name = $request->name;
        $user->alamat = $request->alamat;
        $user->phone = $request->phone;

        if (!empty($request->passwrod)) {
            $user->password = Hash::make($request->password);
        }

        if ($request->foto) {
            // $image = $request->image->getClientOriginalName() . '-' . time() . '.' . $request->image->extension();
            // $request->image->move(public_path('img'), $image);   
            // dd($request->foto);
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
            $foto = $array->image->url;
            $user->foto = $foto;
            // dd($filename);
        }


        $user->update();
        return redirect(route('admin.index'))->with(['Success' => 'Profile telah diperbarui']);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!empty($user)) {
            $user->delete();
            return redirect(route('admin.index'))->with(['Success' => 'Profile telah terhapus']);
        }
        return redirect(route('admin.index'))->with(['Failed' => 'Profile gagal terhapus']);
    }
}
