<?php

namespace App\Http\Controllers\API;

use App\Bank;
use App\Gudang;
use App\Sampah;
use App\Pengepul;
use App\Penjualan;
use Carbon\Carbon;
use App\TransaksiBank;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    public function showPengepul()
    {
        $data = Pengepul::get();

        try {
            return $this->sendResponse('succes', 'Request data has been succesfully get', $data, 200);
        } catch(\Throwable $e) {
            return $this->sendResponse('failed', 'Request data failed to get', null, 500);
        }
    }

    public function sellToPengepul(Request $request) 
    {
        $request->validate([
            'auto_confirm'          => ['sometimes'],
            'pengepul_id'           => ['required', 'exists:pengepuls,id'],
            'lokasi'                => ['required'],
            'sampah'                => ['required'],
            'sampah.*.sampah_id'    => ['required_with:sampah.*.berat', 'exists:App\Sampah,id', 'distinct'],
            'sampah.*.berat'        => ['required_with:sampah.*.sampah_id']
                        ]);
        
        $sampahs = $request->sampah;
            
        foreach($sampahs as $sampah) {
            $stock = Gudang::firstWhere('sampah_id', $sampah['sampah_id'])->total_berat;
            if($stock < $sampah['berat']) {
                $err[] = [ 
                            'sampah_id'         => $sampah['sampah_id'], 
                            'jenis_sampah'      => Sampah::firstWhere('id', $sampah['sampah_id'])->jenis_sampah, 
                            'jumlah_kekurangan' => $stock - $sampah['berat'] 
                         ];
            }            
        }

        if(!empty($err)) {
            return $this->sendResponse('failed', 'The stock quantity of the following items is not sufficient for the demand', collect($err), 400);
        }

        $data = DB::transaction(function() use($request, $sampahs) {   
            $pjl = Penjualan::firstOrCreate([
                'tanggal' => Carbon::now()->toDateString(),
                'pengurus2_id' => Auth::id(),
                'pengepul_id' => $request->pengepul_id,
                'lokasi' => $request->lokasi,
                'status' => 'dalam proses',
            ]);

            foreach($sampahs as $sampah) {
                $harga_j_p = Sampah::firstWhere('id', $sampah['sampah_id'])->harga_jual_perkilogram;
                
                $d_pjl = $pjl->detail_penjualan()->updateOrCreate([
                                                                   'sampah_id' => $sampah['sampah_id'],
                                                                  ],
                                                                  [
                                                                   'berat' => $sampah['berat'],
                                                                   'harga_jual_pengepul' => $harga_j_p,
                                                                   'debit_bank' => $sampah['berat'] * $harga_j_p,
                                                                  ]);
                
            }

            $pjl->update([ 
                          'total_berat_penjualan' => $d_pjl->sum('berat'),
                          'total_debit_bank'      => $d_pjl->sum('debit_bank'),
                         ]);
            
            if( $request->auto_confirm == true ) {
                $this->confirmSaleAsBankTransaction($pjl->id, $request->auto_confirm);
            }
            
            return Penjualan::firstWhere('id', $d_pjl->penjualan_id)->load('detail_penjualan');
        });

        try {
            return $this->sendResponse('succes', 'Sales data has been succesfully created', $data, 200);
        } catch(\Throwable $e) {
            return $this->sendResponse('failed', 'Sales data failed to create', null, 500);
        }
    }

    public function confirmSaleAsBankTransaction($pjl_id, $auto_confirm = false) 
    {
        $pjl = Penjualan::where('id', $pjl_id)
                          ->where('status', 'dalam proses')
                          ->first();
        
        if(empty($pjl)) {
            return $this->sendResponse('failed', 'Sales data not found or has been confirmed', null, 400);
        }
        
        $data = DB::transaction(function() use ($pjl) {

            $dpjl = $pjl->detail_penjualan()->get();

            $dpjl->each(function($item) {
                $gudang = Gudang::firstWhere('sampah_id', $item->sampah_id);
                $gudang->total_berat -= $item->berat;
                $gudang->update();
            });

            TransaksiBank::create([
                'hari/tanggal' => Carbon::now()->toDateTimeString(),
                'pegawai_id' => $pjl->pengurus2_id,
                'keterangan_pengurus' => 'pengurus-dua',
                'keterangan_transaksi' => 'penjualan_bank',
                'penjualan_id' => $pjl->id,
            ]);

            $pjl->status = 'selesai';
            $pjl->update();

            $bank = Bank::firstWhere('id', 1);
            $bank->total_sampah_keluar += $pjl->total_berat_penjualan;
            $bank->total_penjualan_ke_pengepul += $pjl->total_debit_bank;
            $bank->total_saldo += $pjl->total_debit_bank;
            $bank->update();
        });

        if( $auto_confirm == false ) {
            try {
                return $this->sendResponse('succes', 'Sales data has been succesfully created', $data, 200);
            } catch(\Throwable $e) {
                return $this->sendResponse('failed', 'Sales data failed to create', null, 500);
            }
        }
    }

}
