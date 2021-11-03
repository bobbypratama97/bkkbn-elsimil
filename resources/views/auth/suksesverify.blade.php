@extends('layouts.auth')

@section('content')

<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('assets/media/bg/bg-3.jpg');">
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
                        <h3>Selamat</h3>
                        <div class="text-muted font-weight-bold">
                            @if ($data['tipe'] == '1')
                            @if ($data['is_active'] == '4')
                            Akun Anda telah disuspend. <br />Silahkan menghubungi Customer Service
                            @elseif ($data['is_active'] == '5')
                            Akun Anda telah ditolak. <br />Silahkan menghubungi Customer Service
                            @elseif ($data['is_active'] == '3')
                            Akun Anda sedang dalam proses review approval.<br />Terima kasih untuk kesediaannya menunggu.
                            @elseif ($data['is_active'] == '1')
                            Akun Anda telah diapprove dan dapat digunakan
                            @else
                            Akun Anda telah diverifikasi.<br />Akun Anda sedang dalam tahap menunggu approval.<br />Kami akan mengirimkan email persetujuan approval ke email terdaftar Anda.
                            @endif
                            @else
                            Akun Anda telah diverifikasi.<br />Silahkan login dari aplikasi untuk menikmati layanan kami.
                            @endif
                        </div>
                        <div class="text-muted font-weight-bold"></div>
                    </div>
                    @if ($data['tipe'] == '1')
                    <a href="{{ route('login') }}" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Kembali ke halaman Login</a>
                    @endif
                </div>
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>

@endsection