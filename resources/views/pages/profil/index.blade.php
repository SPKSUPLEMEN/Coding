@extends('layouts.app')

@section('content')
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8">

          <div class="section-header">
              <h1>Profil</h1>
              <div class="section-header-breadcrumb">
                  <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                  <div class="breadcrumb-item">Profil</div>
              </div>
          </div>
        </div>
      </div>

        <div class="section-body">
            <div class="row justify-content-center">
              <div class="col-12 col-md-8">
                <div class="card profile-widget">
                  <div class="profile-widget-header">                     
                    <img alt="image" src="assets/img/avatar/avatar-1.png" class="rounded-circle profile-widget-picture">
                  </div>
                  <div class="profile-widget-description">
                    <div class="profile-widget-name">{{Auth::user()->name}} <div class="text-muted d-inline font-weight-normal"><div class="slash"></div>
                    @if(Auth::user()->role == 'admin')
                      Admin
                    @elseif(Auth::user()->role == 'mhs')
                      Pengguna
                    @endif
                    </div></div>
                    Ubah informasi tentang akun anda pada halaman ini.
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                    <h4>Ubah Profil</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('ubah-profil') }}">
                            @csrf                         
                            <div class="row">      
                                <div class="form-group col-md-6 col-12">
                                    <label>Nama</label>
                                    <input type="text" class="form-control" name="name" value="{{$data->name}}">
                                    @if ($errors->has('name'))
                                        <label class="mt-2" style="color: red">
                                            {{ $errors->first('name') }}
                                        </label>
                                    @endif
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label>Email</label>
                                    <input type="text" class="form-control" value="{{$data->email}}" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-12">
                                    <label>Katasandi</label>
                                    <input type="password" class="form-control" name="password">
                                    @if ($errors->has('password'))
                                        <label class="mt-2" style="color: red">
                                            {{ $errors->first('password') }}
                                        </label>
                                    @endif
                                </div>
                                <div class="form-group col-md-6 col-12">
                                    <label>Konfirmasi Katasandi</label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                    @if ($errors->has('password_confirmation'))
                                        <label class="mt-2" style="color: red">
                                            {{ $errors->first('password_confirmation') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
              </div>

            </div>
          </div>
    </section>
@endsection