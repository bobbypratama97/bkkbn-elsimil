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
                        <h3 class="card-title">Tambah Kategori Artikel</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('newskategori.store') }}">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label>Nama Kategori</label>
                                <input type="text" class="form-control" name="name" required oninvalid="this.setCustomValidity('Nama kategori harus diisi')" oninput="setCustomValidity('')" />
                            </div>

                            <div class="form-group">
                                <label>Deskripsi Singkat</label>
                                <textarea class="form-control" rows="5" id="deskripsi" name="deskripsi" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Status Publikasi</label>
                                <select class="form-control select2" id="publikasi" name="publikasi" required oninvalid="this.setCustomValidity('Status publikasi harus dipilih')" oninput="setCustomValidity('')" >
                                    <option value="">Pilih</option>
                                    <option value="1">Draft</option>
                                    <option value="2">Publish</option>
                                </select>
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('newskategori.index') }}" class="btn btn-danger">Batal</a>
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
