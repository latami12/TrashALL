<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function indexNasabah(User $user)
    {
        $nasabahs = $user->where('role_id', 1)->get();

        return view('user.nasabah')->with(compact('nasabahs'));
    }

    public function indexPengurusSatu(User $user)
    {
        $pengurus_satus = $user->where('role_id', 2)->get();

        return view('user.pengurus-satu')->with(compact('pengurus_satus'));
    }

    public function indexPengurusDua(User $user)
    {
        $pengurus_duas = $user->where('role_id', 3)->get();

        return view('user.pengurus-dua')->with(compact('pengurus_duas'));
    }

    public function indexBendahara(User $user)
    {
        $bendaharas = $user->where('role_id', 4)->get();

        return view('user.bendahara')->with(compact('bendaharas'));
    }

    public function delete($user_id)
    {

        $user = User::findOrFail($user_id);
        $role = $user->roles()->first()->name;

        try {
            $user->delete();

            Alert::success('Berhasil', 'Data ' . $role . ' berhasil di hapus');
            return back();
        } catch(\Throwable $e) {
            Alert::error('Gagal', 'Data ' . $role . ' gagal di hapus');
        }
    }

    public function show($user_id)
    {
        User::findOrFail($user_id);
    }

    public function tambahUser(Request $request, Client $client)
    {

        $validatedData = $request->validateWithBag('tambah', [
            'name'            => [ 'required' ,'string'],
            'email'           => [ 'required' ,'email', Rule::unique('users')],
            'password'        => [ 'required', 'min:6' ],
            'no_telephone'    => [ 'required', Rule::unique('users') ],
            'location'        => [ 'required' ],
            'profile_picture' => [ 'image', 'max:2048', 'mimes:jpg,jpeg,png' ],
        ]);

        if(!empty($validatedData['profile_picture'])) {
            $image = base64_encode(file_get_contents($validatedData['profile_picture']));

            $response = $client->request('POST', 'https://freeimage.host/api/1/upload', [
                'form_params' => [
                    'key' => '6d207e02198a847aa98d0a2a901485a5',
                    'action' => 'upload',
                    'source' => $image,
                    'format' => 'json'
                ]
            ]);

            $content = $response->getBody()->getContents();

            $pp = json_decode($content);
            $pp = $pp->image->display_url;
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'no_telephone' => $validatedData['no_telephone'],
            'location' => $validatedData['location'],
            'profile_picture' => $pp,
        ]);

        $userRole = Role::firstWhere('name', $request->user_role);
        $user->roles()->attach($userRole);

        Alert::success('Berhasil', ucfirst($request->user_role) . ' baru berhasil ditambahkan');
        return back();
    }

    public function updateUser(Request $request, Client $client)
    {

        $user = User::findOrFail($request->user_id);

        $validatedData = $request->validateWithBag('edit', [
            'name'            => [ 'nullable', 'string'],
            'email'           => [ 'nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'password'        => [ 'nullable', 'min:6' ],
            'no_telephone'    => [ 'nullable', Rule::unique('users')->ignore($user->id) ],
            'location'        => [ 'nullable' ],
            'profile_picture' => [ 'nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png' ],
        ]);

        if(!empty($validatedData['profile_picture'])) {
            $image = base64_encode(file_get_contents($validatedData['profile_picture']));

            $response = $client->request('POST', 'https://freeimage.host/api/1/upload', [
                'form_params' => [
                    'key' => '6d207e02198a847aa98d0a2a901485a5',
                    'action' => 'upload',
                    'source' => $image,
                    'format' => 'json'
                ]
            ]);

            $content = $response->getBody()->getContents();

            $pp = json_decode($content);
            $pp = $pp->image->display_url;
        } else {
            $pp = $user->profile_picture;
        }

        $input = collect($validatedData)->filter(function($value, $key) {
            return $value != null;
        });

        $input = $input->map(function($value, $key) use($pp) {
            if ( $key == 'password' ) {
                $value = Hash::make($value);
            }
            if( $key == 'profile_picture') {
                $value = $pp;
            }
            return $value;
        });

        $user->update($input->toArray());

        Alert::success('Berhasil', 'Data ' . $user->roles()->first()->name . ' berhasil di update');
        return back();
    }
}
