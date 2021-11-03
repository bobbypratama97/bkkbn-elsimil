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
                @if ($data['status'] == 1)
                <div class="login-signin">
                    <div class="mb-20">
                        <h3>Ubah Password</h3>
                        <div class="text-muted font-weight-bold">Silahkan masukkan password baru Anda.</div>
                    </div>
                    <form class="form" method="POST" action="{{ route('submitchange') }}" >
                        @csrf
                        <input type="hidden" name="id" value="{{ $data['id'] }}">
                        <input type="hidden" name="tipe" value="{{ $data['tipe'] }}">
                        <div class="form-group mb-10">
                            <label>Password Baru</label>
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="password" id="pswd1" placeholder="Isi password Anda" name="password" required oninvalid="this.setCustomValidity('Password harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group mb-10">
                            <label>Konfirmasi Password Baru</label>
                            <input class="form-control form-control-solid h-auto py-4 px-8" type="password" name="confpswd" id="pswd2" placeholder="Isi konfirmasi password Anda" autocomplete="off" required oninvalid="setPasswordConfirmValidity();" oninput="setPasswordConfirmValidity();" />
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Proses</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="login-signin">
                    <div class="mb-20">
                        <h3>Ubah Password</h3>
                        <div class="text-muted font-weight-bold">Link sudah tidak berlaku. Silahkan klik tombol dibawah untuk request verifikasi kembali</div>
                    </div>
                </div>
                @endif
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>

<script>
    function setPasswordConfirmValidity(str) {
        const password1 = document.getElementById('pswd1');
        const password2 = document.getElementById('pswd2');

        if (password1.value === password2.value) {
             password2.setCustomValidity('');
        } else if (password2.value == '') {
            password2.setCustomValidity('Konfirmasi password harus diisi');
        } else {
            password2.setCustomValidity('Password tidak sama');
        }
    }
</script>

@endsection
