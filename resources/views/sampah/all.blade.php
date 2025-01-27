@extends('layouts.main')

@section('title')
    <title>Sampah</title>
@endsection

@section('content')

<form action="{{ route('sampah') }}" >
    <div class="container mt-5">
        <table class="tabel table-hover">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <br>
                    <th scope="col">Kategori Sampah</th>
                    <br>
                    <th scope="col">Harga perkilogram</th>
                    <th scope="col">Harga Jual perkilogram</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                    @foreach ($sampahs as $sampah)
                        <tr>
                            <td>{{ $no++}}</td>
                            <td>{{ $sampah->jenis_sampah}}</td>
                            <td>Rp.{{ $sampah->harga_perkilogram}}</td>
                            <td>Rp.{{ $sampah->harga_jual_perkilogram}}</td>
                            
                        </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
</form>

@endsection
