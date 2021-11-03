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
                        <h3 class="card-title">Ubah Kelurahan</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.kelurahan.update', $lurah->id) }}">
                        @method('PUT')
                        @csrf
                        <div class="card-body">

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Kode Provinsi</label>
                                    <input type="text" class="form-control" value="{{ $lurah->provinsi_kode }}" disabled />
                                </div>
                                <div class="col-lg-6">
                                    <label>Provinsi</label>
                                    <input type="text" class="form-control" value="{{ $lurah->provinsi }}" disabled />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Kode Kabupaten</label>
                                    <input type="text" class="form-control" value="{{ $lurah->kabupaten_kode }}" disabled />
                                </div>
                                <div class="col-lg-6">
                                    <label>Kabupaten</label>
                                    <input type="text" class="form-control" value="{{ $lurah->kabupaten }}" disabled />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Kode Kecamatan</label>
                                    <input type="text" class="form-control" value="{{ $lurah->kecamatan_kode }}" disabled />
                                </div>
                                <div class="col-lg-6">
                                    <label>Kecamatan</label>
                                    <input type="text" class="form-control" value="{{ $lurah->kecamatan }}" disabled />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Kode Kelurahan</label>
                                    <input type="text" class="form-control" value="{{ $lurah->kelurahan_kode }}" disabled />
                                </div>
                                <div class="col-lg-6">
                                    <label>Kelurahan</label>
                                    <input type="text" class="form-control" name="nama" value="{{ $lurah->nama }}" required oninvalid="this.setCustomValidity('Kelurahan harus diisi')" oninput="setCustomValidity('')" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Status Publikasi</label>
                                <select class="form-control select2" id="publikasi" name="publikasi" required oninvalid="this.setCustomValidity('Status publikasi harus dipilih')" oninput="setCustomValidity('')" >
                                    @foreach ($status as $key => $val)
                                    <option value="{{ $key }}" {{ $key == $lurah->status ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.kelurahan.index') }}" class="btn btn-danger">Batal</a>
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
