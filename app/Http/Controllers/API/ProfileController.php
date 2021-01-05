<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\User;
use GuzzleHttp\Client;
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
        // $user->foto = $request->foto;
        $user->phone = $request->phone;

        if (!empty($request->password)) {
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
            $foto = $array->image->url;
            $user->foto = $foto;
            // dd($filename);
        }
        
        $user->update();
        return $this->sendResponse('Success', 'Profile telah terupdate', $user, 200);
    }
}
