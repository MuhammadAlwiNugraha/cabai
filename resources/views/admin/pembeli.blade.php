@extends('layouts.panel')
@section('content')
<section class="content">
      <div class="container-fluid">
       @include('layouts.widgetpembeli')
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-12 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <!-- /.card -->
             <div class="card-body">
                <table class="table table-bordered">
                  <thead>                  
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>No Telepon</th>
                      <th>Kecamatan</th>
                      <th>Alamat</th>
                      <th>KTP</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; ?>
                    @foreach($users as $user)
                    <tr>
                      <td>{{$no++}}</td>
                      <td>{{$user->name}}</td>
                      <td>
                        {{$user->email}}
                      </td>
                      <td>
                        {{$user->telepon}}
                      </td>
                      <td>
                        {{$user->kecamatan}}
                      </td>
                      <td>
                        {{$user->alamat}}
                      </td>
                      <td>
                        <img src="{{asset('ktp/'.$user->ktp)}}" class="img-fluid" alt="Colorlib Template" width="100px" height="100px">
                      </td>
                      <td>
                        @if ($user->status_id == 1)
                           <span class="badge bg-success">aktif</span>
                        @elseif ($user->status_id == 2)
                          <span class="badge bg-danger">banned</span>

                        @else
                          <span class="badge bg-danger">belum aktif</span>

                        @endif
                      </td>
                      <td>
                        <form action="{{ url('/admin/users/delete', $user->id)}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>

                        @if($user->status_id == 0)
                          <a href="{{url('admin/user/aktivasi/'.$user->id)}}" class="btn btn-success btn-sm">Aktifkan</a>
                        @else
                          <a href="{{url('admin/user/nonaktif/'.$user->id)}}" class="btn btn-primary btn-sm">Nonaktifkan</a>
                        @endif
                        
                      </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->

              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  {{ $users->links() }}
                </ul>
              </div>
            </div>
            <!-- /.card -->
            <!-- DIRECT CHAT -->
            
            <!--/.direct-chat -->

            <!-- TO DO List -->
            
            <!-- /.card -->
          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <!-- <section class="col-lg-5 connectedSortable"> -->

            <!-- Map card -->
            
            <!-- /.card -->

            <!-- solid sales graph -->
            
            <!-- /.card -->

            <!-- Calendar -->

            <!-- /.card -->
         <!-- </section> -->
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>

    @endsection