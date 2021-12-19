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
                    <div class="mb-20">
                        <h3>MASUK KE APLIKASI</h3>
                    </div>
                    @if ($errors->has('error'))
                    <div class="alert alert-custom alert-danger" role="alert">
                        <div class="alert-icon">
                            <i class="flaticon-warning"></i>
                        </div>
                        <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                    </div>
                    @endif
                    <form class="form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group mb-5">
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Email atau No Telepon terdaftar" name="login" autocomplete="off" value="{{ old('login') }}" required oninvalid="this.setCustomValidity('Email harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group mb-5">
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Password" name="password" required oninvalid="this.setCustomValidity('Password harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                            <div class="checkbox-inline"></div>
                            <a href="{{ route('password.request') }}" id="kt_login_forgot" class="text-muted text-hover-primary">Lupa Password ?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary  btn-block font-weight-bold btn-lg">Masuk</button>

						<p class="mt-5"><small>Belum Punya Akun?</small> <br><br><a href="{{ route('register') }}" id="kt_login_signup" class="btn btn-success btn-lg btn-block font-weight-bold "> Daftar!</a>
</p>
						
                    </form> 
                </div>
                <!--end::Login Sign in form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>

@endsection
