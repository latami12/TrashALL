<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SampahResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $auth_user = User::firstWhere('id', Auth::id());

        return [
            'id' => $this->id,
            'golongan_sampah_id' => $this->golongan_sampah_id,
            'golongan_sampah' => $this->golonganSampah->golongan,
            'jenis_sampah' => $this->jenis_sampah,
            'harga_perkilogram' => $this->when($auth_user->hasRole('pengurus-satu'), $this->harga_perkilogram),
            $this->mergeWhen($auth_user->hasRole('pengurus-dua'), [
                'jumlah_stock' => $this->gudang->total_berat,
                'harga_jual_perkilogram' => $this->harga_jual_perkilogram,
            ])
        ];
    }
}
