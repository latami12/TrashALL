<?php

namespace App\Http\Controllers;

use App\Http\Resources\TabuganNasabahResource;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function getSaldo()
    {
        $saldos = TabuganNasabahResource::collection(TabunganUser::orderByDesc('id'))->get();

        return view('saldo', compact('saldos'));
    }
}
