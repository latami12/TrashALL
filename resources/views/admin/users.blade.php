@extends('layouts.main')

@section('title')
    <title>Users All</title>
@endsection

@section('content')
<!-- Scrollable modal -->
<form action="{{ route('admin.index') }}">
  <div class="container mt-5">
    <div>
      <li class="nav-item ">
        <a href=""></a>
        <button class="btn btn-danger btn-sm"><i class="fa fa-pencil"> | Create Users</i></button>
      </li>
    </div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">Foto</th>
            <th scope="col">No</th>
            <th scope="col">Role Id</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Alamat</th>
            <th scope="col">Phone</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $no =1; ?>
          @foreach ($users as $user)
          <tr>
            <td><img src="{{ $user->foto}}" class="img-fluid" width="250px" height="100px"></td>
            <th scope="row">{{ $no++}}</th>
            <th scope="row">{{ $user->role_id}}</th>
            <td>{{ $user->name}}</td>
            <td>{{ $user->email}}</td>
            <td>{{ $user->alamat}}</td>
            <td>{{ $user->phone}}</td>
            <td>
                <form action="{{ route('admin.destroy', $user->id) }}" method="post" class="d-inline">
                @csrf
                @method('DELETE')
                {{-- <a href="{{ route('profile.update', $user->id) }} " class="btn btn-warning btn-sm"><i
                        class="fa fa-pencil"> | Edit</i></a> --}}
                <!-- Button trigger modal -->
                  <button class="btn btn-danger btn-sm"><i class="fa fa-trash"> | Hapus</i></button>
                </form>
                  <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                      data-target="#exampleModal{{$user->id}}"> 
                      <i class="fa fa-pencil"> | Edit</i>
                  </button>
        
        
                <!-- Modal -->
                  <div class="modal fade" id="exampleModal{{ $user->id}}" tabindex="-1"
                      aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Edit Users</h5>
                                  <button type="button" class="close" data-dismiss="modal"
                                      aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <form action="{{ route('admin.update', $user->id) }}" target="#" method="post" enctype="multipart/form-data">
                                      @csrf
                                      @method('patch')
                                      <div class="form-group">
                                          <label for="inputAddress">Name</label>
                                          <input type="text" class="form-control" id="inputAddress"
                                              placeholder="Email..." value="{{$user->name}}" name="name">
                                      </div>
                                      <div class="form-group">
                                          <label for="inputAddress">Email</label>
                                          <input type="text" class="form-control" id="inputAddress"
                                              placeholder="Email..." value="{{$user->email}}" name="email">
                                      </div>
                                      <div class="form-group">
                                          <label for="inputAddress">No. Phone</label>
                                          <input type="text" class="form-control" id="inputAddress"
                                              placeholder="No. Phone..." value="{{$user->phone}}" name="phone">
                                      </div>
                                      <div class="form-group">
                                          <label for="inputfile">Foto</label>
                                          <input type="file" class="form-control" id="inputfile"
                                               value="{{$user->foto}}" name="foto">
                                      </div>
                                      <div class="form-group">
                                          <label for="inputCity">City</label>
                                          <input type="text" class="form-control" id="inputCity" placeholder="Address..." value="{{$user->alamat}}" name="alamat">
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary"
                                          data-dismiss="modal">Close</button>
                                      <button type="submit" class="btn btn-primary">Save changes</button>
                                  </div>
                                  </form>
        
                          </div>
                      </div>
                  </div>
                </td>
          </tr>  
          @endforeach
          
          
        </tbody>
      </table>
  </div>
</form>


@endsection