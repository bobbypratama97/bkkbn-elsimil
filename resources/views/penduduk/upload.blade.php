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
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{!! $errors->first('keterangan') !!}</div>
                </div>
                @endif

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Upload Master Penduduk</h3>
                    </div>
                    <form class="form" method="POST" action="{{ route('admin.penduduk.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <div class="form-group">
                                <label>Upload Master Penduduk</label>
                                <input type="file" class="form-control" name="file" required oninvalid="this.setCustomValidity('Pilih dokumen yang diupload terlebih dahulu')" oninput="setCustomValidity('')" accept=".xls, .xlsx" />
                            </div>

                        </div>
                        <div class="card-footer bg-gray-100 border-top-0">
                            <div class="row">
                                <div class="col text-left">
                                    <button type="submit" class="btn btn-primary mr-2">Proses</button>
                                    <a href="{{ route('admin.penduduk.template') }}" class="btn btn-warning">Download Template</a>
                                </div>
                                <div class="col text-right">
                                    <a href="{{ route('admin.penduduk.index') }}" class="btn btn-danger">Batal</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
