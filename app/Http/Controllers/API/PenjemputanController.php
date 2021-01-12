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
        $data = $pj->where('nasabah_id', Auth::id())->with('detail_penjemputan')->get();

        

        try {
            return $this->sendResponse('Success', 'Pickup data has been successfully to get', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Pickup data failed to get', NULL, 500);
        }
    }

    public function requestPenjemputan(Request $request, Penjemputan $pj, DetailPenjemputan $d_pj, Carbon $carbon, Sampah $tabel_sampah, Client $client)
    {
        $tanggal = $carbon->now()->toDateString();
        // $pengurus1_id = $request->pengurus1_id;
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
            'tanggal' => $tanggal,
            'nasabah_id' => Auth::id(),
            // 'pengurus1_id' => $pengurus1_id,
            'status' => 'Menunggu',
            'lokasi' => $lokasi,
            'image' => $image
        ]);

        $data = $old_pj;

        if (!empty($sampahs)) {
            foreach ($sampahs as $sampah) {
                $harga = $tabel_sampah->firstWhere('id', "{$sampah['sampah_id']}");
                $harga_jual = $harga->harga_perkilogram + ($harga->harga_perkilogram * 0.2);
                $d_pj->updateOrCreate(
                    [
                        'penjemputan_id' => $old_pj->id,
                        'sampah_id' => $sampah['sampah_id']
                    ],
                    [
                        'berat' => $sampah['berat'],
                        'harga_perkilogram' => $harga_jual,
                        'harga' => $harga_jual * $sampah['berat']
                    ]
                );
            }

            $old_pj->update([
                'total_berat' => $d_pj->where('penjemputan_id', $old_pj->id)->sum('berat'),
                'total_harga' => $d_pj->where('penjemputan_id', $old_pj->id)->sum('harga'),
            ]);
            $data = $pj->where('id', $old_pj->id)->with('detail_penjemputan')->get();
        }

        return $this->sendResponse('Success', 'Pickup request sent successfully', $data, 201);
    }

    public function batalkanRequestPenjemputan($id)
    {
        $pj = Penjemputan::destroy($id);

        try {
            return $this->sendResponse('Success', 'Pickup data has been successfully deleted', $pj, 201);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Pickup data failed to delete', NULL, 500);
        }
    }
}
