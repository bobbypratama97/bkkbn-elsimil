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
                                                        <a href="#" class="text-warning font-weight-bolder">2 / 10</a>
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
                                    <i class="flaticon-user mr-3"></i><h3 class="card-label"> Data Member </h3>
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
                                    <i class="flaticon2-writing mr-3"></i> <h3 class="card-label">Ulasan Petugas </h3>
                                </div>
                            </div>
                            <form class="form" method="POST" action="{{ route('admin.result.update', $kuis->id) }}">
                            @method('PUT')
                            @csrf
                            <div class="card-body py-4">
                                <div class="form-group">
                                    <label>Ulasan Penilaian</label>
                                    <textarea class="form-control" rows="5" name="komentar" required>{{ (isset($komentar->komentar)) ? $komentar->komentar : '' }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer bg-gray-100 border-top-0">
                                <div class="row">
                                    <div class="col text-right">
                                        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-header h-auto py-4">
                                <div class="card-title">
                                    <i class="flaticon-clipboard mr-3 font-weight-bolder"></i> <h3 class="card-label">Hasil Kuis</h3>
                                </div>
                            </div>
                            <!--begin::Body-->
                            <div class="card-body">
                                <div class="timeline timeline-3">
                                    <div class="timeline-items">
                                        @php 
                                            $i = 1;
                                            $total = count($out);
                                        @endphp
                                        @foreach ($out as $key => $row)
                                        <div class="timeline-item">
                                            <div class="timeline-media" style="background-color: {{ $row['header']['color'] }}; border: 2px solid  {{ $row['header']['color'] }} !important;">
                                                <i class="flaticon2-writing font-weight-bold text-dark"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="mr-2">
                                                        <a href="#" class="text-dark font-weight-bolder">{{ $key }}</a>

                                                        @if (!empty($row['header']['label']))
                                                        <span class="label label-light-success font-weight-bolder label-inline ml-2">Bobot : {{ $row['header']['bobot'] }}</span>
                                                        @else
                                                        <span class="label label-light-success font-weight-bolder label-inline ml-2">Bobot : -</span>
                                                        @endif

                                                        @if (!empty($row['header']['label']))
                                                        <span class="label label-light-danger font-weight-bolder label-inline ml-2">{{ $row['header']['label'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if (!empty($row['header']['formula_value']))
                                                <span class="label label-light-primary font-weight-bolder label-inline mb-5">Hasil : {{ $row['header']['formula_value'] }}</span>
                                                @endif

                                                @foreach ($row['child'] as $keys => $rows)
                                                <p class="p-0 text-primary"><strong>{{ $rows['pertanyaan_detail_title'] }}</strong></p>

                                                @if ($rows['pertanyaan_detail_pilihan'] == 'radio' || $rows['pertanyaan_detail_pilihan'] == 'dropdown')
                                                <p class="p-0 mb-7">{{ $rows['pertanyaan_bobot_label'] }}</p>
                                                @elseif ($rows['pertanyaan_detail_pilihan'] == 'tanggal')
                                                <p class="p-0 mb-7">{!! Helper::customDateMember($rows['value']) !!}</p>
                                                @elseif ($rows['pertanyaan_detail_pilihan'] == 'upload')
                                                <p class="p-0 mb-7"><a class="btn btn-sm btn-success font-weight-bolder mr-2" target="_blank" href="{{ url('uploads/memberfile/' . $rows['value']) }} {{ $rows['pertanyaan_bobot_label'] }}">Lihat Dokumen</a> {{ $rows['value'] }} </p>
                                                @else
                                                <p class="p-0 mb-7">{{ $rows['value'] }}</p>
                                                @endif

                                                @endforeach

                                            </div>
                                        </div>
                                        @php $i++; @endphp
                                        @endforeach
                                    </div>
                                </div>

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
