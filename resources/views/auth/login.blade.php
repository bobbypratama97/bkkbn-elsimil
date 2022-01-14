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
                        <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-80px" alt="" />
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
                    <form id="formlogin" class="form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group mb-5">
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Email atau No Telepon Terdaftar" name="login" id="email" autocomplete="off" value="{{ old('login') }}" required oninvalid="this.setCustomValidity('Email harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group mb-5">
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Password" name="password" required oninvalid="this.setCustomValidity('Password harus diisi')" oninput="setCustomValidity('')" />
                        </div>
						
						 
						<div class="card card-body text-left mb-5">
                        <div class="row ">
                            <div class="col-12">
                            <div class="form-group mb-5">
								<label for="captcha"><strong>Tuliskan kode yang ada pada gambar</strong></label>
                                <div class=" captcha">
                                    <span>{!! captcha_img('mini') !!}</span>
                                    <button type="button" class="btn btn-danger" class="reload" id="reload">
                                    &#x21bb;
                                    </button>
                                </div>
                            </div>
                            </div>
                            <div class="col-12">
                            <div class="form-group mb-5">
                                <input id="captcha" type="text" class="form-control" autocomplete="off" placeholder="4 digit kode" name="captcha" required oninvalid="this.setCustomValidity('Captcha harus diisi')" oninput="setCustomValidity('')">
                            </div>
                            </div>
								
                        </div>
						</div>
                        <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                            <div class="checkbox-inline"></div>
                            <a href="{{ route('password.request') }}" id="kt_login_forgot" class="text-muted text-hover-primary">Lupa Password ?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold btn-lg" >MASUK</button>

						<p class="mt-5"><small>Belum Punya Akun?</small> <br><br><a href="{{ route('register') }}" id="kt_login_signup" class="btn btn-success btn-lg btn-block font-weight-bold "> DAFTAR</a>
                        </p>
						
                    </form> 
                </div>
                <!--end::Login Sign in form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>

@push('script') 
<script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script> 
<script src="{{ asset('assets/plugins/bootbox/bootbox.js') }}"></script> 
<script type="text/javascript">
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
</script>
@endpush
@endsection
