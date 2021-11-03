@extends('layouts.master')

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Card-->
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Data Catin : {{ $output['member']->name }}</h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="{{ url()->previous() }}" class="btn btn-danger font-weight-bolder btn-md">Kembali</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!--begin::Details-->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card-body py-4">
                                    <div class="form-group row my-0">
                                        <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                        <div class="col-11">
                                            <span class="form-control-plaintext font-weight-bolder">Nama : {{ $output['member']->name }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-0">
                                        <label class="col-1 col-form-label"><i class="flaticon2-website"></i></label>
                                        <div class="col-11">
                                            <span class="form-control-plaintext font-weight-bolder">No KTP : {!! Helper::decryptNik($output['member']->no_ktp) !!}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-0">
                                        <label class="col-1 col-form-label"><i class="flaticon-rotate"></i></label>
                                        <div class="col-11">
                                            <span class="form-control-plaintext font-weight-bolder">Gender : {!! Helper::jenisKelamin($output['member']->gender) !!}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body py-4">
                                    <div class="form-group row my-0">
                                        <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                        <div class="col-11">
                                            <span class="form-control-plaintext font-weight-bolder">Usia : {!! Helper::diffDate('', $output['member']->tgl_lahir, 'y') !!}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-0">
                                        <label class="col-1 col-form-label"><i class="flaticon-calendar-with-a-clock-time-tools"></i></label>
                                        <div class="col-11">
                                            <span class="form-control-plaintext font-weight-bolder">Tempat / Tgl Lahir : {{ $output['member']->tempat_lahir }}, {!! Helper::customDateMember($output['member']->tgl_lahir) !!}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row my-0">
                                        <label class="col-1 col-form-label"><i class="flaticon-placeholder-3"></i></label>
                                        <div class="col-11">
                                            <span class="form-control-plaintext font-weight-bolder">Lokasi : {{ $output['member']->kelurahan }}, {{ $output['member']->kecamatan }}, {{ $output['member']->kabupaten }}, {{ $output['member']->provinsi }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Details-->
                    </div>
                </div>
                <!--end::Card-->
                <!--begin::Row-->
                <div class="row">
                    <div class="col-xl-12">
                        <!--begin::Card-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder text-dark">Hasil Kuesioner</span>
                                    <!--<span class="text-muted mt-3 font-weight-bold font-size-sm">More than 400+ new members</span>-->
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-2 mt-n3">
                                <div class="accordion accordion-toggle-arrow" id="accordionExample1">
                                    @php $i = 0; @endphp
                                    @foreach ($output['result'] as $key => $row)
                                    <?php //echo '<pre>'; print_r ($row['header']->rating_color); ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title" data-toggle="collapse" data-target="#collapse{{ $i }}">{{ $row['kuis']['title'] }}</div>
                                        </div>
                                        <div id="collapse{{ $key }}" class="collapse {{ ($i == 0) ? 'show' : '' }}" data-parent="#accordionExample1">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <button type="button" class="btn btn-lg btn-block mb-5" style="background-color: {{ (isset($row['header']->rating_color)) ? $row['header']->rating_color : '#cecece' }};"><span class="text-white text-center font-size-h2 font-weight-boldest">{{ (isset($row['header']->label)) ? $row['header']->label : '-' }}</span></button>
                                                                <button type="button" class="btn btn-success btn-lg btn-block mb-5"><span class="font-weight-boldest">Tgl Pelaksanaan : {!! (isset($row['header']->created_at)) ? $row['header']->created_at : '-' !!}</span></button>
                                                                <button type="button" class="btn btn-primary btn-lg btn-block mb-5"><span class="font-weight-boldest">ID : {{ (isset($row['header']->kuis_code)) ? $row['header']->kuis_code : '-' }}</span></button>
                                                                <button type="button" class="btn btn-warning btn-lg btn-block mb-5"><span class="font-weight-boldest">{{ (isset($row['header']->member_kuis_nilai)) ? $row['header']->member_kuis_nilai : '-' }} / {{ (isset($row['header']->kuis_max_nilai)) ? $row['header']->kuis_max_nilai : '-' }} </span></button>
                                                            </div>
                                                            <div class="col-lg-12">
                                                                <div class="card card-custom bg-light-success card-shadowless gutter-b">
                                                                    <div class="card-body my-3">
                                                                        <a class="card-title font-weight-bolder text-success text-hover-state-dark font-size-h6 mb-4 d-block">Ulasan Pendamping</a>
                                                                        <div class="font-weight-bold font-size-md">
                                                                            {{ (isset($row['komentar']['komentar'])) ? $row['komentar']['komentar'] : '-' }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="card card-custom card-shadowless gutter-b bg-light">
                                                            <div class="card-body pt-8">

                                                                <div class="timeline timeline-3">
                                                                    <div class="timeline-items">
                                                                        @php 
                                                                            $j = 1;
                                                                            $total = count($row['out']);
                                                                        @endphp
                                                                        @foreach ($row['out'] as $keys => $rows)
                                                                        <div class="timeline-item">
                                                                            <div class="timeline-media" style="background-color: {{ $rows['header']['color'] }}; border: 2px solid  {{ $rows['header']['color'] }} !important;">
                                                                                <i class="flaticon2-writing font-weight-bold text-dark"></i>
                                                                            </div>
                                                                            <div class="timeline-content">
                                                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                                                    <div class="mr-2">
                                                                                        <a href="#" class="text-dark font-weight-bolder">{{ $keys }}</a>

                                                                                        @if (!empty($rows['header']['label']))
                                                                                        <span class="label label-light-danger font-weight-bolder label-inline ml-2">{{ $rows['header']['label'] }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                @if (!empty($rows['header']['formula_value']))
                                                                                <span class="label label-light-primary font-weight-bolder label-inline mb-5">Hasil : {{ $rows['header']['formula_value'] }}</span>
                                                                                @endif

                                                                                @foreach ($rows['child'] as $keyz => $rowz)
                                                                                <p class="p-0 text-primary"><strong>{{ $rowz['pertanyaan_detail_title'] }}</strong></p>

                                                                                @if ($rowz['pertanyaan_detail_pilihan'] == 'radio' || $rowz['pertanyaan_detail_pilihan'] == 'dropdown')
                                                                                <p class="p-0 mb-7">{{ $rowz['pertanyaan_bobot_label'] }}</p>
                                                                                @elseif ($rowz['pertanyaan_detail_pilihan'] == 'tanggal')
                                                                                <p class="p-0 mb-7">{!! Helper::customDateMember($rowz['value']) !!}</p>
                                                                                @elseif ($rowz['pertanyaan_detail_pilihan'] == 'upload')
                                                                                <p class="p-0 mb-7"><a class="btn btn-sm btn-success font-weight-bolder mr-2" target="_blank" href="{{ url('uploads/memberfile/' . $rowz['value']) }} {{ $rowz['pertanyaan_bobot_label'] }}">Lihat Dokumen</a> {{ $rowz['value'] }} </p>
                                                                                @else
                                                                                <p class="p-0 mb-7">{{ $rowz['value'] }}</p>
                                                                                @endif

                                                                                @endforeach

                                                                            </div>
                                                                        </div>
                                                                        @php $j++; @endphp
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php $i++; @endphp
                                    @endforeach
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

@endsection
