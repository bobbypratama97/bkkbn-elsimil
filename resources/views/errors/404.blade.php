@extends('layouts.auth')

@section('content')

<div class="d-flex flex-column flex-root">
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('assets/media/bg/bg-3.jpg');">
            <div class="login-form text-center p-7 position-relative overflow-hidden">
                <div class="d-flex flex-center mb-15">
                    <a href="#">
                        <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-70px" alt="" />
                    </a>
                </div>
                <div class="login-signin">
                    <div class="mb-10">
                        <h4 class="text-danger font-weight-bolder">Halaman yang Anda cari tidak ditemukan</h4>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection