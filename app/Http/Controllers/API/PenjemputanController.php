<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Penjemputan;
use App\DetailPenjemputan;
use App\Sampah;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PenjemputanController extends Controller
{
    public function requestPenjemputan(Request $request, Penjemputan $pj, DetailPenjemputan $d_pj, Carbon $carbon, Sampah $tabel_sampah)
    {
        $tanggal = $carbon->now()->toDateString();
        $pengurus1_id = $request->pengurus1_id;
        $lokasi = $request->lokasi;
        $sampahs = $request->sampah;

        $old_pj = $pj->firstOrCreate([
            'tanggal' => $tanggal,
            'nasabah_id' => Auth::id(),
            'pengurus1_id' => $pengurus1_id,
            'status' => 'Menunggu',
            'lokasi' => $lokasi
        ]);

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
        }

        $data = $pj->where('id', $old_pj->id)->with('detail_penjemputan')->get();
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
