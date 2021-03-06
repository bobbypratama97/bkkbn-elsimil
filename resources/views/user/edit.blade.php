@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet"/>
<style>
    .customPos { margin-left: auto !important; margin-right: auto !important; display: block !important; margin: 0px !important; }
    .customPosImg { border-radius: 0px !important; margin-left: auto !important; margin-right: auto !important; margin: 0px !important; width: 270px !important; height: 250px !important; }
</style>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                </div>
                @endif

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Data Admin CMS : {{ $user->name }}</h3>
                    </div>

                    <form method="POST" action="{{ route('admin.user.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" class="form-control" value="{!! Helper::decryptNik($user->nik) !!}" name="nik" disabled />
                            </div>

                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" name="name" required/>
                            </div>

                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" class="form-control" value="{{ $user->no_telp }}" name="no_telp" required />
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label> &nbsp; <span style="font-size: 9px;font-weight: bold;"><input type="checkbox" onchange="changePassword(this)" id="is_change_password" name="is_change_password"/> Ubah Password</span>
                                        <input id="password" type="password" class="form-control" value="00000000" name="password" required disabled />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Konfirmasi Password</label>
                                        <input id="confirm_password" type="password" class="form-control" value="00000000" name="password_confirmation" required disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{ $user->email }}" name="email" required />
                            </div>

                            <div class="form-group">
                                <label>Nomor SK</label>
                                <input type="text" class="form-control" value="{{ $user->no_sk }}" name="no_sk" />
                            </div>
                            <div class="form-group">
                                <label>Sertifikat</label>
                                <textarea type="text" class="form-control" name="sertifikat" />{{ $user->sertifikat }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Provinsi</label>
                                        <input type="text" class="form-control" value="{{ $user->provinsi }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kabupaten</label>
                                        <input type="text" class="form-control" value="{{ $user->kabupaten }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <input type="text" class="form-control" value="{{ $user->kecamatan }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Kelurahan</label>
                                        <input type="text" class="form-control" value="{{ $user->kelurahan }}" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" id="publikasi" name="status">
                                    @foreach ($status as $key => $val)
                                    <option value="{{ $key }}" {{ ($key == $user->is_active) ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <select class="form-control select2" id="roles" name="role" onchange="roleChange(event)">
                                    @foreach ($roles as $key => $val)
                                    <option value="{{ $val->id }}" {{ ($user->role_id == $val->id) ? 'selected' : '' }}>{{ $val->name }}</option>
                                    @endforeach
                                </select>
                                <label></label>
                                <select class="form-control select2" id="rolechild" name="rolechild" style="display: block;">
                                <option value="">-</option>
                                @foreach($role_childs as $rolechild)
                                <option value="{{$rolechild['value']}}" {{ ($user->role_child_id == $rolechild['value']) ? 'selected' : '' }}>{{$rolechild['name']}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                @if(Auth::user()->role < $user->role_id || $user->id == Auth::user()->id)
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                </div>
                                @else
                                <div class="col text-left">
                                    <button type="button" class="btn btn-default mr-2 btn-disabled">Simpan</button>
                                </div>
                                @endif
                                <div class="col text-right">
                                    <a href="{{ route('admin.user.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#roles').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#publikasi').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#rolechild').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });
    });

    function roleChange(e) {
        let id = e.target.value
        let base_url = window.location.origin + '/' + window.location.pathname.split ('/') [1] + '/';

        $.get(base_url+'user-management/role/'+id+'/child', function(res) {
            var select = document.getElementById("rolechild");
            select.innerHTML = ''
            if(res.length > 0){
                res.forEach(element => {
                    let option = document.createElement("option");
                    option.text = element.name;
                    option.value = element.id;
                    select.appendChild(option);
                });
                // document.getElementById("rolechild").style.display = "block";
            }else{
                // document.getElementById("rolechild").style.display = "none";
            }
        })
    }

    function changePassword(data){
        var password = document.getElementById('password')
        var confirm_password = document.getElementById('confirm_password')
        if($('#is_change_password').is(':checked')){
            $('#password').val('')
            $('#confirm_password').val('')
            password.removeAttribute('disabled')
            confirm_password.removeAttribute('disabled')
        }else{
            $('#password').val('00000000')
            $('#confirm_password').val('00000000')
            password.setAttribute('disabled','disabled')
            confirm_password.setAttribute('disabled','disabled')
        }
    }

</script>
@endpush

@endsection
