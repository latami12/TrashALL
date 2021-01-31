<?php

namespace App\Http\Controllers\API;

use App\Bank;
use App\User;
use App\Gudang;
use App\Sampah;
use App\Transaksi;
use Carbon\Carbon;
use App\Penyetoran;
use App\Penjemputan;
use App\TabunganUser;
use App\TransaksiBank;
use App\DetailPenyetoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PenyetoranController extends Controller
{
    public function showNasabahRequest()
    {
        $pj = Penjemputan::where('status', 'Menunggu')
            ->with(['nasabah', 'detail_penjemputan'])
            ->get();

        return $this->sendResponse('Success', 'Request data has been succesfully get', $pj, 200);
    }

    public function showAcceptedRequest()
    {
        $pj = Penjemputan::where('status', 'Diterima')
            ->where('pengurus1_id', Auth::id())
            ->with(['nasabah', 'detail_penjemputan'])
            ->get();

        return $this->sendResponse('Success', 'Request data has been succesfully get', $pj, 200);
    }

    public function acceptNasabahRequest($pj_id)
    {
        $pj = Penjemputan::where('status', 'Menunggu')
            ->findOrFail($pj_id);

        $data = $pj->load('nasabah');

        $pj->update([
            'status'       => 'Diterima',
            'pengurus1_id' => Auth::id()
        ]);

        return $this->sendResponse('Success', 'Request data has been succesfully get', $data, 200);
    }

    public function declineNasabahRequest($pj_id)
    {
        $pj = Penjemputan::findOrFail($pj_id)->update(['status' => 'Ditolak']);

        return $this->sendResponse('Success', 'Request data succesfully decline', $pj, 200);
    }

    public function searchNasabah($keyword = null)
    {
        $user = new User;

        $users = $user->when(!empty($keyword), function ($q) use ($keyword) {
            $q->where('role_id', 1)
                ->where(function ($q) use ($keyword) {
                    return $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', "{$keyword}")
                        ->orWhere('phone', "{$keyword}");
                });
        }, function () use ($user) {
            return $user->where('role_id', 1);
        })->get();

        return $this->sendResponse('Success', 'Users data has been succesfully get', $users, 200);
    }


    public function penyetoranNasabah(Request $request)
    {
        $request->validate([
            'auto_confirm'          => ['sometimes'],
            'nasabah_id'            => [
                                        'required',
                                        'user_role:role,nasabah'
                                       ],
            'keterangan_penyetoran' => ['required', Rule::in(['dijemput', 'diantar'])],
            'penjemputan_id'        => [
                                        'required_if:keterangan_penyetoran,dijemput',
                                        Rule::exists('penjemputans', 'id')->where(function($query) use ($request) {
                                            $query->where('nasabah_id', $request->nasabah_id)
                                                    ->where('pengurus1_id', Auth::id())
                                                    ->where('status', 'diterima');
                                        }),
                                       ],
            'lokasi'                => ['required'],
            'sampah'                => ['required'],
            'sampah.*.sampah_id'    => ['required_with:sampah.*.berat', 'exists:App\Sampah,id', 'distinct'],
            'sampah.*.berat'        => ['required_with:sampah.*.sampah_id'],
        ]);

        try {

            DB::beginTransaction();

            $pt = Penyetoran::firstOrCreate([
                'tanggal'               => Carbon::now()->toDateString(),
                'nasabah_id'            => $request->nasabah_id,
                'pengurus1_id'          => Auth::id(),
                'keterangan_penyetoran' => $request->keterangan_penyetoran,
                'penjemputan_id'        => $request->keterangan_penyetoran == 'dijemput' ? $request->penjemputan_id : null,
                'lokasi'                => $request->lokasi,
                'status'                => "dalam proses",
            ]);

            $sampahs = $request->sampah;
            foreach($sampahs as $sampah) {
                $harga_perkilogram = Sampah::firstWhere('id', $sampah['sampah_id'])->harga_perkilogram;
                $harga_jemput = $harga_perkilogram + ($harga_perkilogram * 0.2);
                $pt->detail_penyetoran()->updateOrCreate([
                                                            'sampah_id'     => $sampah['sampah_id'],
                                                         ],
                                                         [
                                                            'berat'         => $sampah['berat'],
                                                            'harga'         => $request->keterangan_penyetoran == 'dijemput'
                                                                                    ? $harga_jemput
                                                                                    : $harga_perkilogram,
                                                            'debit_nasabah' => $request->keterangan_penyetoran == 'dijemput'
                                                                                    ? $harga_jemput * $sampah['berat']
                                                                                    : $harga_perkilogram * $sampah['berat'],
                                                         ]);
            }

            $d_pt = DetailPenyetoran::where('penyetoran_id', $pt->id)->get();

            $pt->total_berat = $d_pt->sum('berat');
            $pt->total_debit = $d_pt->sum('debit_nasabah');
            $pt->update();

            if( (bool) $request->auto_confirm == true ) {
                $this->confirmDepositAsTransaksi($pt->id, $request->auto_confirm);
            }

            DB::commit();

            $data = $pt->firstWhere('id', $pt->id)->load('detail_penyetoran');


            return $this->sendResponse('succes', 'Request data has been succesfully get', $data, 200);
        } catch(\Throwable $e) {
            report($e);
            DB::rollback();

            return $this->sendResponse('failed', 'Request data failed to create', null, 500);
        }
    }

    public function showPenyetoranNasabah()
    {
        $data = Penyetoran::where('pengurus1_id', Auth::id())
            ->where('status', 'dalam proses')
            ->with('detail_penyetoran')
            ->get();

        return $this->sendResponse('Success', 'Deposit data has been succesfully get', $data, 200);
    }

    public function confirmDepositAsTransaksi($penyetoran_id, $auto_confirm = false)
    {
        $pt = Penyetoran::where('id', $penyetoran_id)
            ->where('status', 'dalam proses')
            ->first();

        if (empty($pt)) {
            return $this->sendResponse('Failed', 'Deposit data not found or has been confirmed', null, 400);
        }

        $data = DB::transaction(function () use ($pt) {

            $tanggal = Carbon::now()->toDateTimeString();

            $transaksi = Transaksi::create([
                'tanggal' => $tanggal,
                'nasabah_id' => $pt->nasabah_id,
                'keterangan_transaksi' => $pt->keterangan_penyetoran,
                'penyetoran_id' => $pt->id,
                'debet' => $pt->total_debit,
            ]);

            $pt->update(['status' => 'selesai']);

            $dpts = $pt->detail_penyetoran()->get()->toArray();

            $tabunganUser = new TabunganUser;
            $sampah = new Sampah;
            $gudang = new Gudang;

            foreach ($dpts as $key => $value) {

                $jenis_sampah = $sampah->firstWhere('id', $dpts[$key]['sampah_id'])->jenis_sampah;

                $oldStock = $gudang->firstOrCreate(['sampah_id' => $dpts[$key]['sampah_id']]);

                $gudang->updateOrCreate(
                    ['sampah_id' => $dpts[$key]['sampah_id']],
                    ['total_berat' => $oldStock->total_berat + $dpts[$key]['berat']]
                );


                $oldTabunganUser = $tabunganUser->latest('id')->where('nasabah_id', $transaksi->nasabah_id)->first();
                $tabunganUser->create([
                    'nasabah_id' => $transaksi->nasabah_id,
                    'transaksi_id' => $transaksi->id,
                    'hari/tanggal' => $tanggal,
                    'keterangan' => $transaksi->keterangan_transaksi,
                    'jenis_sampah' => $jenis_sampah,
                    'berat' => $dpts[$key]['berat'],
                    'debet' => $dpts[$key]['debit_nasabah'],
                    'saldo' => empty($oldTabunganUser->saldo) ? $dpts[$key]['debit_nasabah']
                        : $oldTabunganUser->saldo + $dpts[$key]['debit_nasabah'],
                ]);
            }

            TransaksiBank::create([
                'tanggal' => $tanggal,
                'pegawai_id' => $pt->pengurus1_id,
                'keterangan_pengurus' => 'pengurus-satu',
                'keterangan_transaksi' => 'debet_nasabah',
                'transaksi_id' => $transaksi->id,
            ]);

            $bank = Bank::firstWhere('id', 1);
            $bank->total_sampah_masuk += $pt->total_berat;
            $bank->total_debit_nasabah += $transaksi->debet;
            $bank->update();

            if ($transaksi->keterangan_transaksi == 'dijemput') {
                Penjemputan::where('id', $pt->penjemputan_id)->update(['status' => 'berhasil']);
            }

            return $transaksi;
        });

        if ($auto_confirm != true) {
            try {
                return $this->sendResponse('Success', 'Deposit data has been succesfully confirmed', $data, 200);
            } catch (\Throwable $e) {
                return $this->sendResponse('Failed', 'Deposit data failed to confirm', null, 500);
            }
        }
    }
}
