@extends('layouts.master')

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Card-->
                <div class="card card-custom gutter-b">
                    <div class="card-body">
                        <!--begin::Details-->
                        <div class="d-flex">
                            <!--begin: Pic-->
                            <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                <div class="card-body p-0 rounded mb-3 text-center bg-light-primary">
                                    <div class="row m-0">
                                        <div class="col-12 p-0">
                                            <div class="card card-custom card-stretch card-transparent card-shadowless">
                                                <div class="pt-5 pl-5 pr-5 pb-5 d-flex flex-column justify-content-center">
                                                    <h3 class="font-size-h6 font-size-h4-sm font-size-h4-lg font-size-h4-xl mb-0">
                                                        <a href="#" class="text-primary font-weight-bolder">ID : {{ $kuis->kuis_code }}</a>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body rounded text-center bg-light-warning">
                                    <div class="row m-0">
                                        <div class="col-12">
                                            <div class="card card-custom card-stretch card-transparent card-shadowless">
                                                <div class="card-body d-flex flex-column justify-content-center" style="padding: 0;">
                                                    <h1 class="mb-0" style="font-size: 3rem;">
                                                        <a href="#" class="text-warning font-weight-bolder">{{ $kuis->member_kuis_nilai }} / {{ $kuis->kuis_max_nilai }}</a>
                                                    </h1>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Pic-->
                            <!--begin::Info-->
                            <div class="flex-grow-1">
                                <!--begin::Title-->
                                <div class="d-flex justify-content-between flex-wrap mt-1 mb-3">
                                    <div class="d-flex mr-3">
                                        <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-2">{{ $kuis->kuis_title }}</a>
                                    </div>
                                    <div class="my-lg-0 my-3">
                                        <a href="{{ route('admin.result.index') }}" class="btn btn-sm btn-danger font-weight-bolder text-uppercase">Kembali</a>
                                    </div>
                                </div>

                                <!--end::Title-->
                                <!--begin::Content-->
                                <div class="d-flex flex-wrap justify-content-between mt-1">
                                    <div class="d-flex flex-column flex-grow-1 pr-8">
                                        <div class="d-flex flex-wrap mb-4">
                                            <a href="#" class="text-success text-hover-primary font-weight-bolder mr-lg-8 mr-5 mb-lg-0 mb-2">
                                            <i class="flaticon2-calendar mr-2 text-success font-size-lg"></i>Tanggal Pelaksanaan Kuesioner : {{ $kuis->tanggal }}</a>
                                        </div>
                                        <span class="font-weight-bold text-dark-50">{!! (isset($deskripsi->deskripsi)) ? Str::words($deskripsi->deskripsi, 70, ' ...') : '' !!}</span>
                                    </div>
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::Details-->
                    </div>
                </div>
                <!--end::Card-->
                <!--begin::Row-->
                <div class="row">
                    <div class="col-xl-4">
                        <!--begin::Card-->
                        <div class="card card-custom mb-5">
                            <!--begin::Header-->
                            <div class="card-header h-auto py-4">
                                <div class="card-title">
                                    <h3 class="card-label">Data Member </h3>
                                    <!--<span class="d-block text-muted pt-2 font-size-sm">company profile preview</span></h3>-->
                                </div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body py-4">
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{!! Helper::customUser($member->name) !!}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-phone"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{{ $member->no_telp }}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-email"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{{ $member->email }}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{!! Helper::jenisKelamin($member->gender) !!}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-map"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{{ $member->alamat }}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-map"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{{ $member->kelurahan }}, {{ $member->kecamatan }}, {{ $member->kabupaten }}, {{ $member->provinsi }}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon-feed"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">{{ $member->kodepos }}</span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card-->

                        <div class="card card-custom">
                            <div class="card-header h-auto py-4">
                                <div class="card-title">
                                    <h3 class="card-label">Ulasan Petugas </h3>
                                </div>
                            </div>
                            <div class="card-body py-4">
                                <div class="form-group">
                                    <label>Ulasan Penilaian</label>
                                    <textarea class="form-control" rows="5" name="deskripsi" required></textarea>
                                </div>
                            </div>
                            <div class="card-footer bg-gray-100 border-top-0">
                                <div class="row">
                                    <div class="col text-right">
                                        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <!--begin::Card-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Hasil Kuis</span>
                                    <!--<span class="text-muted mt-3 font-weight-bold font-size-sm">More than 400+ new members</span>-->
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-2 pb-0 mt-n3">
                                <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Kuisioner</th>
                                            <th>Jawaban Member</th>
                                            <th>Label</th>
                                            <th width="11%">Bobot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($result as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $row['caption'] }}</td>
                                            <td>{{ $row['value'] }}</td>
                                            <td>{{ $row['label'] }}</td>
                                            <td>{{ $row['bobot'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
                <!--end::Row-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
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
