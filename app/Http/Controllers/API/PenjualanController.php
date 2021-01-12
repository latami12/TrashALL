<?php

namespace App\Http\Controllers\API;

use App\Bank;
use App\Gudang;
use App\Http\Controllers\Controller;
use App\Pengepul;
use App\Penjualan;
use App\Sampah;
use App\TransaksiBank;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function showPengepul()
    {
        $data = Pengepul::all();

        try {
            return $this->sendResponse('Success', 'Request data has been successfully to get', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Request data failed to get', NULL, 200);
        }
    }

    public function sellToPengepul(Request $request)
    {
        $sampahs = $request->sampah;

        foreach($sampahs as $sampah){
            $stock = Gudang::firstWhere('sampah_id', $sampah['sampah_id'])->total_berat;
            if ($stock < $sampah['berat']) {
                $err[] = [
                    'sampah_id'         => $sampah['berat'],
                    'jenis_sampah'      => Sampah::firstWhere('id', $sampah['sampah_id'])->jenis_sampah,
                    'jumlah_kekurangan' => $stock - $sampah['berat']
                ];
            }
        }

        if (!empty($err)) {
            return $this->sendResponse('Failed', 'the stock is not sufficient for the demand', collect($err), 400);
        }

        $data = DB::transaction(function() use($request, $sampahs) {
            $pjl = Penjualan::firstOrCreate([
                'tanggal'       => Carbon::now()->toDateString(),
                'pengurus2_id'  => Auth::id(),
                'pengepul_id'   => $request->pengepul_id,
                'lokasi'        => $request->lokasi,
                'status'        => 'dalam proses'
            ]);

            foreach ($sampahs as $sampah) {
                $harga_j_p = Sampah::firstWhere('id', $sampah['sampah_id'])->harga_jual_perkilogram;

                $d_pjl = $pjl->detail_penjualan()->updateOrCreate([
                    'sampah_id' =>$sampah['sampah_id'],
                ],
                [
                    'berat' => $sampah['berat'],
                    'harga_jual_pengepul' =>$harga_j_p,
                    'debit_bank' => $sampah['berat'] * $harga_j_p,
                ]);
            }

            $pjl->update([
                'total_berat_penjualan' => $d_pjl->sum('berat'),
                'total_debit_bank' => $d_pjl->sum('debit_bank'),
            ]);

            if ($request->auto_confirm == true) {
                $this->confirmSaleAsBankTransaction($pjl->id, $request->auto_confirm);
            }

            return Penjualan::firstWhere('id', $d_pjl->penjualan_Id)->load('detail_penjualan');
        });

        try {
            return $this->sendResponse('Success', 'Sales data has been successfully created', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Sales data failed to created', NULL, 200);
        }
    }

    public function confirmSaleAsBankTransaction($pjl_id, $auto_confirm = false)
    {
        $pjl = Penjualan::where('id', $pjl_id)
                        ->where('status', 'dalam proses')
                        ->first();

        if (empty($pjl)) {
            return $this->sendResponse('Failed', 'Sales data not found or has been confirmed', NULL, 400);
        }

        $data = DB::transaction(function() use($pjl){
            $dpjl = $pjl->detail_penjualan()->get();

            $dpjl->each(function($item){
                $gudang = Gudang::firstWhere('sampah_id', $item->sampah_id);
                $gudang->total_berat -= $item->berat;
                $gudang->update();
            });

            TransaksiBank::create([
                'hari/tanggal'          => Carbon::now()->toDateString(),
                'pegawai_id'            => $pjl->pengurus2_id,
                'keterangan_pengurus'   => 'pengurus_dua',
                'keterangan_transaksi'  => 'penjualan_bank',
                'penjualan_id'          => $pjl->id,
            ]);

            $pjl->status = 'selesai';
            $pjl->update();

            $bank = Bank::firstWhere('id',1);
            $bank->total_sampah_keluar += $pjl->total_berat_penjualan;
            $bank->total_penjualan_ke_pengepul += $pjl->total_debit_bank;
            $bank->total_saldo += $pjl->total_debit_bank;
            $bank->update();
        });

        if ($auto_confirm == false) {
            try {
                return $this->sendResponse('Success', 'Sales data has been successfully created', $data, 200);
            } catch (\Throwable $th) {
                return $this->sendResponse('Failed', 'sales data failed to create', NULL, 500);
            }
        }
    }
}
