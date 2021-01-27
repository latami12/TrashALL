<?php

namespace App\Http\Controllers\API;

use App\TabunganUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TabuganNasabahResource;

class NasabahController extends Controller
{
    public function getTabungan()
    {
        $tabungan = TabuganNasabahResource::collection(TabunganUser::orderByDesc('id')->where('nasabah_id', Auth::id())->get());

        return $this->sendResponse('Success', 'Data Tabungan successfully get', $tabungan, 200);
    }
}
