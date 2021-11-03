@extends('layouts.auth')

@section('content')

<div class="d-flex flex-column flex-root">
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('assets/media/bg/bg-3.jpg');">
            <div class="login-form text-center p-7 position-relative overflow-hidden">
                <div class="d-flex flex-center mb-15">
                    <a href="#">
                        <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-100px" alt="" />
                    </a>
                </div>
                <div class="login-signin">
                    <div class="mb-20">
                        <h3>PEMANTAUAN HASIL KUESIONER CATIN</h3>
                    </div>
                    @if ($errors->has('error'))
                    <div class="alert alert-custom alert-danger" role="alert">
                        <div class="alert-icon">
                            <i class="flaticon-warning"></i>
                        </div>
                        <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                    </div>
                    @endif
                    <form class="form" method="POST" action="{{ route('kua.result') }}">
                        @csrf
                        <div class="text-center mb-5">Cari hasil kuesioner catin</div>
                        <div class="input-group input-group-lg">
                            <input type="text" name="kode" class="form-control input-lg mr-5" placeholder="ID profile atau kode kuis catin" style="border-top-right-radius:10px !important; border-bottom-right-radius:10px !important;" required oninvalid="this.setCustomValidity('Pencarian harus diisi')" oninput="setCustomValidity('')" >
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-lg" type="submit"><i class="flaticon-search"></i> Cari</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection