<?php

namespace App\Http\Controllers\API;

use App\Bank;
use App\Gudang;
use App\Sampah;
use App\Transaksi;
use Carbon\Carbon;
use App\Penyetoran;
use App\Penjemputan;
use App\TabunganUser;
use App\DetailPenyetoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Inline\Parser\BangParser;

class PenyetoranController extends Controller
{
    public function showNasabahRequest(Penjemputan $pj)
    {
        // dd(Auth::id());
        $data = $pj->where('pengurus1_id', Auth::id())
                   ->where('status', 'Menunggu')
                   ->with('detail_penjemputan')
                   ->get();

        try {
            return $this->sendResponse('Success', 'Request data has been succesfully to get', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Request data failed to get', NULL, 500);
        }           
    }

    public function acceptNasabahRequest($pj_id, Penjemputan $pj)
    {
        $pj = $pj->where('id', $pj_id)
                 ->where('pengurus1_id', Auth::id())
                 ->where('status', 'menunggu')
                 ->first();

        if (!empty($pj)) {
            $pj->update(['status', 'diterima']);
        }         

        try {
            return $this->sendResponse('Success', 'Request has been successfully to get', $pj, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('failed', 'Request failed to get', NULL, 500);
        }
    }

    public function penyetoranNasabah(Request $request, Penyetoran $pt, DetailPenyetoran $d_pt)
    {
        $data = DB::transaction(function() use($request, $pt, $d_pt){
            $pt = $pt->firstOrCreate([
                'tanggal'               => Carbon::now()->toDateString(),
                'nasabah_id'            => $request->nasabah_id,
                'pengurus1_id'          => Auth::id(),
                'keterangan_penyetoran' => $request->keterangan_penyetoran == 'dijemput' ? $request->keterangan_penyetoran : NULL,
                'lokasi'                => $request->lokasi,
                'status'                => "dalam proses",
            ]);

            $sampahs = $request->sampah;

            foreach($sampahs as $sampah){
                $harga_perkilogram = Sampah::firstWhere('id', $sampah['sampah_id'])->harga_perkilogram;
                $harga_jemput = $harga_perkilogram + ($harga_perkilogram * 0.2);
                $pt->detail_penyetoran()->updateOrCreate([
                    'sampah_id'         => $sampah['sampah_id'],
                ],
                [
                    'berat'             => $sampah['berat'],
                    'harga'             => $request->keterangan_penyetoran == 'dijemput'
                                                        ? $harga_jemput
                                                        : $harga_perkilogram,
                    'debit_nasabah'     => $request->keterangan_penyetoran == 'dijemput'
                                                        ? $harga_jemput * $sampah['berat']
                                                        : $harga_perkilogram * $sampah['berat'],                                     
                ]);
                
            }

            $pt->update([
                'total_berat' => $d_pt->where('penyetoran_id', $pt->id)->sum('berat'),
                'total_debit' => $d_pt->where('penyetoran_id', $pt->id)->sum('debit_nasabah'),
            ]);

            if ($request->auto_confirm == true) {
                $this->confirmDepositAsTransaksi($pt->id, $request->auto_confirm);
            }

            return $pt->firstWhere('id', $pt->id)->load('detail_penyetoran');
        });

        try {
            return $this->sendResponse('Success', 'Request data has been Successfully to get', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Request data failed to get', NULL, 500);
        }
    }

    public function showPenyetoranSampah(Penyetoran $pt)
    {
        $data = $pt->where('pengurus1_id', Auth::id())
                   ->where('status', 'dalam proses')
                   ->with('detail_penyetoran')
                   ->get();

        try {
            return $this->sendResponse('Success', 'Deposit data has been successfully to get', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse('Failed', 'Deposit data failed to get', NULL, 500);
        }           
    }

    public function confrimDepositAsTransaksi($penyetoran_id, $auto_confirm = false)
    {
        $pt = Penyetoran::where('id', $penyetoran_id)
                          ->where('status', 'dalam proses')
                          ->first();

        if (empty($pt)) {
            return $this->sendResponse('Failed', 'Deposit data failed to get', NULL, 500);
        }                  
        
        $data = DB::transaction(function() use($pt) {
            $transaksi = Transaksi::create([
                'tanggal'               => Carbon::now()->toDateString(),
                'nasabah_id'            => $pt->nasabah_id,
                'keterangan_transaksi'  => $pt->keterangan_transaksi,
                'penyetoran_id'         => $pt->id,
                'debit'                 => $pt->total_debit,
            ]);

            $pt->update(['status', 'selesai']);

            $dpts = $pt->detail_penyetoran()->get()->toArray();

            $tabunganUser = new TabunganUser;
            $sampah = new Sampah;
            $gudang = new Gudang;

            foreach($dpts as $key => $value){
                $jenis_sampah = $sampah->firstWhere('id', $dpts[$key]['sampah_id'])->jenis_sampah;

                $oldStock = $gudang->firstOrCreate(['sampah_id' => $dpts[$key]['sampah_id']]);

                $gudang->updateOrCreate(
                    [ 'sampah_id' => $dpts[$key]['sampah_id'] ],
                    [ 'total_berat' => $oldStock->total_berat + $dpts[$key]['sampah_id'] ]
                );

                $oldTabunganUser = $tabunganUser->latest('id')->where('transaksi_id', $transaksi->id)->first();

                $tabunganUser->create([
                    'nasabah_id'    => $transaksi->nasabah_id,
                    'transaksi_id'  => $transaksi->id,
                    'hari/tanggal'  => $transaksi->tanggal,
                    'keterangan'    => $transaksi->keterangan_transaksi,
                    'jenis_sampah'  => $jenis_sampah,
                    'berat'         => $dpts[$key]['berat'],
                    'debit'         => $dpts[$key]['debit_nasabah'],
                    'saldo'         => empty($oldTabunganUser->saldo) ? $dpts[$key]['debit_nasabah']
                                                                      : $oldTabunganUser->saldo + $dpts[$key]['debit_nasabah'],
                ]);
            }

            $bank = new Bank;
            $bank->total_debit_nasabah += $transaksi->debit;
            $bank->save();

            if ($transaksi->keterangan_transaksi == 'dijemput') {
                Penjemputan::where('id', $pt->penjemputan_id)->update(['status', 'berhasil']);
            }

            return $transaksi;
        });

        if ( $auto_confirm != true) {
            try {
                return $this->sendResponse('Success', 'Deposit data has been successfully confirmed', $data, 200);
            } catch (\Throwable $th) {
                return $this->sendResponse('Failed', 'Deposit data failed to confirm', NULL, 500);
            }
        }
    }
}
