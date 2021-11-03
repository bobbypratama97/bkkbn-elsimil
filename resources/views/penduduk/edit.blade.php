@extends('layouts.master')

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
                        <h3 class="card-title">Ubah Provinsi</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.provinsi.update', $provinsi->id) }}">
                        @method('PUT')
                        @csrf
                        <div class="card-body">

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label>Kode Provinsi</label>
                                    <input type="text" class="form-control" value="{{ $provinsi->provinsi_kode }}" disabled />
                                </div>
                                <div class="col-lg-6">
                                    <label>Provinsi</label>
                                    <input type="text" class="form-control" name="nama" value="{{ $provinsi->nama }}" required oninvalid="this.setCustomValidity('Provinsi harus diisi')" oninput="setCustomValidity('')" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Status Publikasi</label>
                                <select class="form-control select2" id="publikasi" name="publikasi" required oninvalid="this.setCustomValidity('Status publikasi harus dipilih')" oninput="setCustomValidity('')" >
                                    @foreach ($status as $key => $val)
                                    <option value="{{ $key }}" {{ $key == $provinsi->status ? 'selected' : '' }}>{{ $val }}</option>
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
                                    <a href="{{ route('admin.provinsi.index') }}" class="btn btn-danger">Batal</a>
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
