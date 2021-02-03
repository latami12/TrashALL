<?php

namespace App\Http\Controllers;

use App\Sampah;
use Illuminate\Http\Request;
use App\Http\Resources\SampahResource;

class SampahController extends Controller
{
    public function getSampah()
    {
        $sampahs = SampahResource::collection(Sampah::get());

        return view('sampah.all', compact('sampahs'));
    }

    public function delete($sampah_id)
    {

        $sampah = Sampah::findOrFail($sampah_id);

        try {
            $sampah->delete();

            Alert::success('Berhasil', 'Data sampah berhasil di hapus');
            return back();
        } catch(\Throwable $e) {
            Alert::error('Gagal', 'Data sampah gagal di hapus');
        }
    }
}
