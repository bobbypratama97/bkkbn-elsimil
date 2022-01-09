@extends('layouts.auth')

@section('content')

<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('../assets/media/bg/bg-3.jpg');">
            <div class="login-form text-center p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <a href="#">
                        <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-100px" alt="" />
                    </a>
                </div>
                <!--end::Login Header-->
                <!--begin::Login Sign in form-->
                <div class="login-signin">
                    <div class="mb-20">
                        <h3>Lupa Password ?</h3>
                        <div class="text-muted font-weight-bold">Silahkan masukkan email anda atau nomor telepon untuk mengatur kata sandi baru pada link yang akan kami kirim.</div>
                    </div>
                    @if ($errors->has('error'))
                    <div class="alert alert-custom alert-danger" role="alert">
                        <div class="alert-icon">
                            <i class="flaticon-warning"></i>
                        </div>
                        <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                    </div>
                    @endif
                    <form class="form" id="kt_login_forgot_form" method="POST" action="{{ route('forgot') }}" >
                        @csrf
                        <input type="hidden" name="tipe" value="1">
                        <div class="form-group mb-10">
                            <input class="form-control form-control-solid h-auto py-4 px-8" type="text" placeholder="Isi dengan Email atau Nomor Telepon yang valid" name="email" autocomplete="off" value="{{ old('email') }}" required oninvalid="this.setCustomValidity('Email harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button id="kt_login_forgot_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Request</button>
                        </div>
                    </form>
                    <div class="mt-10 text-center">
                        <span class="opacity-70 mr-4">Sudah punya akun ?</span>
                        <a href="{{ route('login') }}" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">Masuk!</a>
                    </div>
                </div>
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>

@endsection
