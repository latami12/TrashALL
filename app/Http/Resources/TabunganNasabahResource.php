<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TabuganNasabahResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'transaksi_id' => $this->transaksi_id,
            'tanggal' => $this->created_at->translatedFormat('l, d F Y'),
            'waktu' => $this->created_at->translatedFormat('H:i'),
            'keterangan' => ucwords($this->keterangan),
            'jenis_sampah' => $this->jenis_sampah,
            'berat' => $this->berat,
            'debet' => $this->debet,
            'kredit' => $this->kredit,
            'saldo' => $this->saldo,
        ];
    }
}