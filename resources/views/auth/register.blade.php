@extends('layouts.auth')

@section('content')

<div class="d-flex flex-column flex-root"> 
  <!--begin::Login-->
  <div class="login login-4 login-signin-on d-flex flex-row-fluid">
    <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('assets/media/bg/bg-3.jpg');">
      <div class="login-form p-7 position-relative overflow-hidden"> 
        <!--begin::Login Header-->
        <div class="d-flex flex-center mb-15"> <a href="#"> <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-80px" alt="" /> </a> </div>
        <!--end::Login Header--> 
        <!--begin::Login Sign in form-->
        <div>
          <div class="text-center mb-20">
            <h3>Pendaftaran Akses</h3>
            <div class="text-muted font-weight-bold">Tuliskan Data Anda secara Lengkap dan Benar.</div>
          </div>
          @if ($errors->any())
          <div class="alert alert-custom alert-danger" role="alert">
            <div class="alert-icon"> <i class="flaticon-warning"></i> </div>
            <div class="alert-text"> <strong>Perhatian</strong>
              <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
          @endif
          <form id="formregis" class="form" method="POST" action="{{ route('register') }}" onsubmit="return confirms('formregis')">
            @csrf
            <fieldset>
              <legend><strong>Informasi Umum dan Login</strong></legend>
              <div class="form-group mb-5">
                <label class="pl-8">Nama Lengkap</label>
                <input class="form-control h-auto form-control py-4 px-8" type="text" placeholder="Isi Nama Lengkap sesuai KTP" name="name" id="name" value="{{ old('name') }}" autocomplete="off" required oninvalid="this.setCustomValidity('Nama lengkap harus diisi')" oninput="setCustomValidity('')" />
              </div>
              <div class="form-group">
                <label class="pl-8">Nomor KTP</label>
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Isi dengan NO KTP" name="nik" id="nik" value="{{ old('nik') }}" aria-describedby="basic-addon2">
                  <!-- <div class="input-group-append" id="search"> <span class="input-group-text"> <i class="la la-search icon-lg"></i> </span> </div> -->
                </div>
              </div>
              <div class="form-group mb-5">
                <label class="pl-8">Nomor Telepon</label>
                <input class="form-control h-auto form-control py-4 px-8" type="text" placeholder="Isi dengan Nomor Telepon yang valid" name="no_telp" value="{{ old('no_telp') }}" autocomplete="off" required oninvalid="this.setCustomValidity('No Telepon harus diisi')" oninput="setCustomValidity('')" />
              </div>
              <div class="form-group mb-5">
                <label class="pl-8">Email</label>
                <input class="form-control h-auto form-control py-4 px-8" type="email" placeholder="Isi dengan Email yang Valid (Opsional)" name="email" id="email" value="{{ old('email') }}" autocomplete="off" />
              </div>
              <div class="form-group mb-5">
                <label class="pl-8">Password</label>
                <input class="form-control h-auto form-control py-4 px-8" type="password" minlength="6" placeholder="Password untuk Elsimil (minimal 6 Karakter)" name="password" required oninvalid="this.setCustomValidity('Password harus diisi')" oninput="setCustomValidity('')" />
              </div>
            </fieldset>
            <fieldset>
              <legend><strong>Informasi Penugasan</strong></legend>
              <div class="form-group mb-5">
                <label class="pl-8">Nomor SK</label>
                <input class="form-control h-auto form-control py-4 px-8" type="text" placeholder="Isi Nomor SK Tim Pendamping Keluarga" name="no_sk" value="{{ old('no_sk') }}"/>
              </div>
              <div class="form-group mb-5">
                <label class="pl-8">Sertifikat</label>
                <textarea class="form-control h-auto form-control py-4 px-8" placeholder="Tuliskan Sertifikat Pelatihan Pendampingan Keluarga yang Pernah Anda Dapatkan." name="sertifikat" / >{{ old('sertifikat') }}</textarea>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="form-group mb-5">
                    <label class="">Provinsi</label>
                    <select class="form-control select2" id="provinsi" name="provinsi_id" required  oninvalid="this.setCustomValidity('Provinsi harus diisi')" oninput="setCustomValidity('')">
                    </select>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group mb-5">
                    <label class="">Kabupaten</label>
                    <select class="form-control select2" id="kabupaten" name="kabupaten_id"  required oninvalid="this.setCustomValidity('Kabupaten harus diisi')" oninput="setCustomValidity('')">
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="form-group mb-5">
                    <label class="">Kecamatan</label>
                    <select class="form-control select2" id="kecamatan" name="kecamatan_id" required oninvalid="this.setCustomValidity('Kecamatan harus diisi')" oninput="setCustomValidity('')">
                    </select>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group mb-5">
                    <label class="">Kelurahan</label>
                    <select class="form-control select2" id="kelurahan" name="kelurahan_id" required oninvalid="this.setCustomValidity('Kelurahan harus diisi')" oninput="setCustomValidity('')">
                    </select>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset>
              <legend><strong>Mendaftar Sebagai ?</strong></legend>
              <div class="form-group">
                <select class="form-control select2" id="roles" name="role" onchange="roleChange(event)" required oninvalid="this.setCustomValidity('Role harus diisi')" oninput="setCustomValidity('')">
                </select>
                <label></label>
                <select class="form-control select2" id="rolechild" name="rolechild" style="display: block;" >
                </select>
              </div>
            </fieldset>
			  
			  
			  
			  
            <fieldset>
              <div class=" card card-body card-sm">
				  <div class="row">
				  <div class="col-12">
                  <div class="form-group mb-5">
					  <label for="captcha"><strong>Tuliskan kode yang ada pada gambar.</strong></label>
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
                     <input id="captcha" type="text" class="form-control" placeholder="4 Digit Kode" name="captcha" required oninvalid="this.setCustomValidity('Captcha harus diisi')" oninput="setCustomValidity('')">
                  </div>
                </div>
              </div>
				  </div>
            </fieldset>
            <div class="form-group d-flex flex-wrap flex-center mt-10">
              <button type="submit" class="btn btn-primary btn-block font-weight-bold px-9 py-4 my-3 mx-2" return confirms('member-update')>SIMPAN</button>
            </div>
          </form>
          <div class="mt-10 text-center"> <a href="{{ route('password.request') }}" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">Lupa Password ?</a> </div>
          <div class="mt-10 text-center"> <span class="opacity-70 mr-4">Sudah Pernah Mendaftar?</span> <a href="{{ route('login') }}" id="kt_login_signup" class="text-muted text-hover-primary font-weight-bold">MASUK</a> </div>
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
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });

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
            placeholder: "Pilih Petugas Pendamping / Admin",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#rolechild').select2({
            placeholder: "Pilih Tim Pendamping Keluarga",
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
                select.innerHTML = '<option value="">Pilih Tim Pendamping Keluarga</option>'
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

    function confirms(form_title)
    {
        let form = document.forms[form_title];
        let email = $('#email').val()
        let name   = email.substring(0, email.lastIndexOf("@"));
        let domain = email.substring(email.lastIndexOf("@") +1);
        let regisdomain = ['gmail.com', 'yahoo.com', 'gmail.co.id'];

        if(!regisdomain.includes(domain)){
            bootbox.confirm({
                title: 'Perhatian',
                message: "<p class='text-center'>Mohon dipastikan kembali email yang anda masukan tidak ada kesalahan penulisan.</p>",
                centerVertical: true,
                closeButton: false,
                buttons: {
                    confirm: { label: 'Yakin', className: 'btn-success' },
                    cancel: { label: 'Batalkan', className: 'btn-danger' }
                },
                callback: function (result) {
                    if(result == true){
                        form.submit()
                        return true
                    }else{
                        return true
                    }
                }
            });
        }else{
            form.submit()
        }
        return false
    }
</script> 
@endpush

@endsection 