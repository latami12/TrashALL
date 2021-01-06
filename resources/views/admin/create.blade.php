@extends('layouts.main')

@section('title')
<title>Users All</title>
@endsection

@section('content')
<form action="{{ route('user') }}">
    <div class="container mt-5">
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">@</span>
            <input type="text" class="form-control" placeholder="Username.." aria-label="Username"
                aria-describedby="basic-addon1">
        </div>
        
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">@</span>
            <input type="text" class="form-control" placeholder="Email.." aria-label="Email"
                aria-describedby="basic-addon1">
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputCity">Password</label>
                <input type="password" class="form-control" id="inputCity" placeholder="Password..." value="" name="alamat">
            </div>
            <div class="form-group col-md-6">
                <label for="inputState">Password Confirmation</label>
                <input type="password" class="form-control" value="" name="umur">
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-primary"><i class="fa fa-pencil"> | Create</i></button>
        </div>
    </div>
</form>
@endsection
