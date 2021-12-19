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
                        <h3 class="card-title">Data Admin CMS : {{ $user->name }}</h3>
                        <div class="card-toolbar">
                            <div class="example-tools justify-content-center">
                                <a href="{{ route('admin.user.index') }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label>NIK</label>
                            <input type="text" class="form-control" value="{!! Helper::decryptNik($user->nik) !!}" disabled />
                        </div>

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" disabled />
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{ $user->email }}" disabled />
                        </div>

                        <div class="form-group">
                            <label>Nomor SK</label>
                            <input type="text" class="form-control" value="{{ $user->no_sk }}" disabled />
                        </div>
                        <div class="form-group">
                            <label>Sertifikat</label>
                            <textarea type="text" class="form-control" disabled />{{ $user->sertifikat }}</textarea>
                        </div>

                        <div class="form-group row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Provinsi</label>
                                    <input type="text" class="form-control" value="{{ $user->provinsi }}" disabled />
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Kabupaten</label>
                                    <input type="text" class="form-control" value="{{ $user->kabupaten }}" disabled />
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <input type="text" class="form-control" value="{{ $user->kecamatan }}" disabled />
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Kelurahan</label>
                                    <input type="text" class="form-control" value="{{ $user->kelurahan }}" disabled />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <input type="text" class="form-control" value="{{ Helper::statusAdmin($user->is_active) }}" disabled />
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" value="{{ $user->roles }}" disabled />
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#kategori').select2({
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
    });
</script>
@endpush

@endsection
