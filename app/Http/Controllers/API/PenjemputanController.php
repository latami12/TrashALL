<?php

namespace App\Http\Controllers\API;

use App\Sampah;
use Carbon\Carbon;
use App\Penjemputan;
use GuzzleHttp\Client;
use App\DetailPenjemputan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PenjemputanController extends Controller
{
    public function showRequestPenjemputan(Penjemputan $pj)
    {
        $data = $pj->latest()
            ->where('nasabah_id', Auth::id())
            ->with(['nasabah:id,name,email,phone', 'pengurus_satu:id,name,email,phone,alamat,foto', 'detail_penjemputan'])
            ->get()
            ->groupBy('status');

        return $this->sendResponse('succes', 'Pickup data has been succesfully get', $data, 200);
    }

    public function requestPenjemputan(Request $request, Penjemputan $pj, DetailPenjemputan $d_pj, Sampah $tabel_sampah, Client $client)
    {
        $request->validate([
            'lokasi' => 'required',
            'image'  => 'required|image'
        ]);

        $lokasi = $request->lokasi;
        $sampahs = $request->sampah;

        $image = base64_encode(file_get_contents($request->image));

        $res = $client->request('POST', 'https://freeimage.host/api/1/upload', [
            'form_params' => [
                'key' => '6d207e02198a847aa98d0a2a901485a5',
                'action' => 'upload',
                'source' => $image,
                'format' => 'json'
            ]
        ]);

        $image = json_decode($res->getBody()->getContents());

        $image = $image->image->display_url;

        $old_pj = $pj->firstOrCreate([
            'tanggal'       => Carbon::now()->toDateString(),
            'nasabah_id'    => Auth::id(),
            'status'        => 'Menunggu',
            'lokasi'        => $lokasi,
            'image'         => $image
        ]);

        $data = $old_pj;

        if(!empty($sampahs)) {
            foreach($sampahs as $sampah) {
                $harga = $tabel_sampah->firstWhere('id', "{$sampah['sampah_id']}")->harga_perkilogram;
                $harga_j = $harga + ($harga * 0.2);
                $d_pj->updateOrCreate([
                                        'penjemputan_id'    => $old_pj->id,
                                        'sampah_id'         => $sampah['sampah_id'],
                                      ],
                                      [
                                        'berat'             => $sampah['berat'],
                                        'harga_perkilogram' => $harga_j,
                                        'harga'             => $harga_j * $sampah['berat'],
                                      ]);
            }
            
            $old_pj->update([
                'total_berat' => $d_pj->where('penjemputan_id', $old_pj->id)->sum('berat'),
                'total_harga' => $d_pj->where('penjemputan_id', $old_pj->id)->sum('harga'),
            ]);
            $data = $pj->where('id', $old_pj->id)->with('detail_penjemputan')->get();
        }
        
        return $this->sendResponse('succes', 'Pickup request sent successfully', $data, 201);
    }

    public function batalkanBarangRequestPenjemputan($id, Penjemputan $pj, DetailPenjemputan $d_pj)
    {

        $d_pj = $d_pj->firstWhere('id', $id);

        if (empty($d_pj) || $pj->firstWhere('id', $d_pj->penjemputan_id)->status != 'Menunggu') {
            return $this->sendResponse('failed', 'Pickup data not found or cannot be deleted', null, 400);
        }

        $pj_id = $d_pj->penjemputan_id;
        $d_pj->delete();

        $pj->where('id', $pj_id)->update([
            'total_berat' => $d_pj->where('penjemputan_id', $pj_id)->sum('berat'),
            'total_harga' => $d_pj->where('penjemputan_id', $pj_id)->sum('harga'),
        ]);

        try {
            return $this->sendResponse('succes', 'Pickup data has been succesfully deleted', (bool) $d_pj, 200);
        } catch (\Throwable $e) {
            return $this->sendResponse('failed', 'Pickup data failed to delete', null, 500);
        }
    }

    public function batalkanRequestPenjemputan($id)
    {
        $pj = Penjemputan::where('id', $id)->where('status', 'Menunggu')->first();
        if (!empty($pj)) {
            $d_pj = DetailPenjemputan::where('penjemputan_id', $pj->id)->get();
            if (!empty($d_pj)) {
                $pj->detail_penjemputan()->delete();
            }

            $pj->where('id', $id)->delete();

            return $this->sendResponse('succes', 'Pickup data has been succesfully deleted', true, 200);
        } else {
            return $this->sendResponse('failed', 'Pickup data cannot be deleted', false, 404);
        }
    }
}
