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
                    <div class="mb-5">
                        <h3>Verifikasi Email Anda</h3>
                        <div class="text-muted font-weight-bold">
                            Kami telah mengirimkan link verifikasi akun ke email Anda. Silahkan verifikasi akun Anda terlebih dahulu.<br /><br /><br />
                            Jika Anda tidak menerima email, silahkan klik tombol dibawah untuk mengirimkan ulang verifikasi ke email Anda
                        </div>
                        <div class="text-muted font-weight-bold"></div>
                    </div>
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">{{ __('Kirim ulang link verifikasi') }}</button>
                    </form>
                </div>
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>

@endsection