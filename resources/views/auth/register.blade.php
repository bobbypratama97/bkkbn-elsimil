@extends('layouts.auth')

@section('content')

<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('assets/media/bg/bg-3.jpg');">
            <div class="login-form p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <a href="#">
                        <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-100px" alt="" />
                    </a>
                </div>
                <!--end::Login Header-->
                <!--begin::Login Sign in form-->
                <div>
                    <div class="text-center mb-20">
                        <h3>Pendaftaran Akses</h3>
                        <div class="text-muted font-weight-bold">Tuliskan informasi diri anda secara lengkap dan benar.</div>
                    </div>
                    @if ($errors->any())
                    <div class="alert alert-custom alert-danger" role="alert">
                        <div class="alert-icon">
                            <i class="flaticon-warning"></i>
                        </div>
                        <div class="alert-text">
                            <strong>Perhatian</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <form class="form" method="POST" action="{{ route('register') }}" >
                        @csrf
                        <div class="form-group">
                            <label class="pl-8">NIK</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Isi dengan NIK" name="nik" id="nik" value="{{ old('nik') }}" aria-describedby="basic-addon2">
                                <div class="input-group-append" id="search">
                                    <span class="input-group-text">
                                        <i class="la la-search icon-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Nama Lengkap</label>
                            <input class="form-control h-auto form-control py-4 px-8" type="text" placeholder="Isi dengan Nama Lengkap" name="name" id="name" value="{{ old('name') }}" autocomplete="off" required oninvalid="this.setCustomValidity('Nama lengkap harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Email</label>
                            <input class="form-control h-auto form-control py-4 px-8" type="email" placeholder="Isi dengan Email yang valid" name="email" value="{{ old('email') }}" autocomplete="off" required oninvalid="this.setCustomValidity('Email harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Password</label>
                            <input class="form-control h-auto form-control py-4 px-8" type="password" placeholder="Isi password Anda" name="password" required oninvalid="this.setCustomValidity('Password harus diisi')" oninput="setCustomValidity('')" />
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Nomor SK</label>
                            <input class="form-control h-auto form-control py-4 px-8" type="text" placeholder="Isi Nomor SK Anda" name="no_sk" />
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Sertifikat</label>
                            <textarea class="form-control h-auto form-control py-4 px-8" placeholder="Tulis sertifikat yang pernah anda dapatkan dipisahi dengan koma(,)" name="sertifikat" /></textarea>
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Provinsi</label>
                            <select class="form-control select2" id="provinsi" name="provinsi_id" required required oninvalid="this.setCustomValidity('Provinsi harus diisi')" oninput="setCustomValidity('')">
                            </select>
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Kabupaten</label>
                            <select class="form-control select2" id="kabupaten" name="kabupaten_id" required required oninvalid="this.setCustomValidity('Kabupaten harus diisi')" oninput="setCustomValidity('')">
                            </select>
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Kecamatan</label>
                            <select class="form-control select2" id="kecamatan" name="kecamatan_id" required required oninvalid="this.setCustomValidity('Kecamatan harus diisi')" oninput="setCustomValidity('')">
                            </select>
                        </div>
                        <div class="form-group mb-5">
                            <label class="pl-8">Kelurahan</label>
                            <select class="form-control select2" id="kelurahan" name="kelurahan_id" required required oninvalid="this.setCustomValidity('Kelurahan harus diisi')" oninput="setCustomValidity('')">
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="pl-8">Roles</label>
                            <select class="form-control select2" id="roles" name="role" onchange="roleChange(event)" required oninvalid="this.setCustomValidity('Role harus diisi')" oninput="setCustomValidity('')">
                            </select>

                            <label></label>
                            <select class="form-control select2" id="rolechild" name="rolechild" style="display: block;" >
                            </select>
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">SIMPAN</button>
                        </div>
                    </form>
                    <div class="mt-10 text-center">
                        <a href="{{ route('password.request') }}" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">Lupa Password ?</a>
                    </div>
                    <div class="mt-10 text-center">
                        <span class="opacity-70 mr-4">Sudah punya akun ?</span>
                        <a href="{{ route('login') }}" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">Masuk!</a>
                    </div>
                </div>
                <!--end::Login Sign up form-->
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
    $(document).ready(function() {
        $('#provinsi').select2({
            placeholder: "Pilih Provinsi",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#kabupaten').select2({
            placeholder: "Pilih Kabupaten",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#kecamatan').select2({
            placeholder: "Pilih Kecamatan",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#kelurahan').select2({
            placeholder: "Pilih Kelurahan",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#roles').select2({
            placeholder: "Pilih Role",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#rolechild').select2({
            placeholder: "Pilih Petugas",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $.ajax({
            type: "POST",
            url: '{{ route('provinsi') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                $('#provinsi').html(data.content);
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.ajax({
            type: "POST",
            url: '{{ route('getrole') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                $('#roles').html(data.content);
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $('#search').on('click', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var nik = $('#nik').val();

            if (nik == '') {
                bootbox.alert({
                    title: 'Perhatian',
                    message: "<p class='text-center'>NIK harus diisi</p>",
                    centerVertical: true,
                    closeButton: false,
                    buttons: {
                        ok: { label: 'OK', className: 'btn-danger' }
                    }
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: '{{ route('carinik') }}',
                    data: { "_token": "{{ csrf_token() }}", "nik": nik },
                    dataType: "json",
                    success: function(data) {
                        if (data.count == '0') {
                            bootbox.alert({
                                title: 'Perhatian',
                                message: "<p class='text-center'>NIK tidak terdaftar. Pastikan kembali NIK Anda</p>",
                                centerVertical: true,
                                closeButton: false,
                                buttons: {
                                    ok: { label: 'OK', className: 'btn-danger' }
                                }
                            });
                        }

                        $('#name').val(data.nama);
                        $('#provinsi').html(data.provinsi);
                        $('#kabupaten').html(data.kabupaten);
                        $('#kecamatan').html(data.kecamatan);
                        $('#kelurahan').html(data.kelurahan);
                    },
                    failure: function(errMsg) {
                        alert(errMsg);
                    }
                });
            }

            $.preloader.stop();
        });

        $('#provinsi').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var provinsi = $('#provinsi').val();
            $.ajax({
                type: "POST",
                url: '{{ route('kabupaten') }}',
                data: { "_token": "{{ csrf_token() }}", "provinsi_id": provinsi },
                dataType: "json",
                success: function(data) {
                    $('#kabupaten').html(data.content);
                    $('#kecamatan').html('');
                    $('#kelurahan').html('');
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });

            $.preloader.stop();
        });

        $('#kabupaten').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kabupaten = $('#kabupaten').val();
            $.ajax({
                type: "POST",
                url: '{{ route('kecamatan') }}',
                data: { "_token": "{{ csrf_token() }}", "kabupaten_id": kabupaten },
                dataType: "json",
                success: function(data) {
                    $('#kecamatan').html(data.content);
                    $('#kelurahan').html('');
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });

            $.preloader.stop();
        });

        $('#kecamatan').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kecamatan = $('#kecamatan').val();
            $.ajax({
                type: "POST",
                url: '{{ route('kelurahan') }}',
                data: { "_token": "{{ csrf_token() }}", "kecamatan_id": kecamatan },
                dataType: "json",
                success: function(data) {
                    $('#kelurahan').html(data.content);
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });

            $.preloader.stop();
        });
    });

    function roleChange(e) {
        let id = e.target.value
        let route = 'rolechild/'+id

        $('#rolechild').removeAttr('required')

        $.get(route, function(res) {
            var select = document.getElementById("rolechild");
            if(res.length > 0){
                select.innerHTML = '<option value="">Pilih Petugas</option>'
                res.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.name;
                    option.value = element.id;
                    select.appendChild(option);
                });
                // document.getElementById("rolechild").style.display = "block";
                $('#rolechild').attr('required', '')
            }else{
                select.innerHTML = ''
                let option = document.createElement("option");
                option.text = '-';
                option.value = '-1';
                select.appendChild(option);

                // document.getElementById("rolechild").style.display = "none";
            }
        })
    }
</script>
@endpush

@endsection
