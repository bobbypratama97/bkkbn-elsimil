@extends('layouts.auth')

@section('content')

    <div class="d-flex flex-column flex-root">
        <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
            <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('assets/media/bg/bg-3.jpg');">
                <div class="login-form text-center p-7 position-relative overflow-hidden">
                    <div class="d-flex flex-center mb-15">
                        <a href="#">
                            <img src="{{ asset('assets/media/logos/logo-new.png') }}" class="max-h-100px" alt="" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom gutter-b example-hover bg-light">
                            <div class="card-header">
                                <div class="card-title">
                                </div>
                                <div class="card-toolbar">
                                    @if ($output['couple'] > 0)
                                    <a href="{{ route('kua.couples', $output['member']->id) }}" class="btn btn-success font-weight-bolder mr-3">Lihat Pasangan</a>
                                    @else
                                    <a class="btn btn-secondary font-weight-bolder unclick mr-3">Belum punya pasangan</a>
                                    @endif
                                    <a href="{{ route('kua.index') }}" class="btn btn-danger font-weight-bolder">Kembali</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if ($output['count'] == '0')
                                    <div class="col-lg-12 text-center">
                                        Data pasangan tidak ditemukan
                                    </div> 
                                    @else
                                    <div class="col-lg-6">
                                        <div class="card-body py-4">
                                            <div class="form-group row my-0">
                                                <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                                <div class="col-10">
                                                    <span class="form-control-plaintext font-weight-bolder">Nama : {{ $output['member']->name }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row my-0">
                                                <label class="col-1 col-form-label"><i class="flaticon2-website"></i></label>
                                                <div class="col-10">
                                                    <span class="form-control-plaintext font-weight-bolder">ID Profile : {{ $output['member']->profile_code }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row my-0">
                                                <label class="col-1 col-form-label"><i class="flaticon-rotate"></i></label>
                                                <div class="col-10">
                                                    <span class="form-control-plaintext font-weight-bolder">Gender : {!! Helper::jenisKelamin($output['member']->gender) !!}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card-body py-4">
                                            <div class="form-group row my-0">
                                                <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                                <div class="col-10">
                                                    <span class="form-control-plaintext font-weight-bolder">Usia : {!! Helper::diffDate('', $output['member']->tgl_lahir, 'y') !!}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row my-0">
                                                <label class="col-1 col-form-label"><i class="flaticon-calendar-with-a-clock-time-tools"></i></label>
                                                <div class="col-10">
                                                    <span class="form-control-plaintext font-weight-bolder">Tempat / Tgl Lahir : {{ $output['member']->tempat_lahir }}, {!! Helper::customDateMember($output['member']->tgl_lahir) !!}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row my-0">
                                                <label class="col-1 col-form-label"><i class="flaticon-placeholder-3"></i></label>
                                                <div class="col-10">
                                                    <span class="form-control-plaintext font-weight-bolder">Lokasi : {{ $output['member']->kelurahan }}, {{ $output['member']->kecamatan }}, {{ $output['member']->kabupaten }}, {{ $output['member']->provinsi }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom gutter-b example-hover">
                            <div class="card-body" style="padding: 0 !important;">
                                @if (empty($output['result']))
                                    <div class="row"><div class="col-lg-12 text-center">Belum ada kuesioner yang diikuti oleh pasangan</div></div>
                                @else
                                <div class="accordion accordion-toggle-arrow" id="accordionExample1">
                                    @php $i = 0; @endphp
                                    @foreach ($output['result'] as $key => $row)
                                    <?php //echo '<pre>'; print_r ($row['header'][0]); ?>
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
                                                            <div class="col-lg-12 mb-5">
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
                                                                            <div class="timeline-content mb-10">
                                                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                                                    <div class="mr-2">
                                                                                        <a href="#" class="text-dark font-weight-bolder"><i class="flaticon-medal mr-3"></i> {{ str_replace('Widget ', '', $keys) }}</a>

                                                                                        @if (!empty($rows['header']['label']))
                                                                                        <span class="label label-light-danger font-weight-bolder label-inline ml-2">{{ $rows['header']['label'] }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                @if (!empty($rows['header']['formula_value']))
                                                                                <span class="label label-light-primary font-weight-bolder label-inline mb-5">Hasil : {{ $rows['header']['formula_value'] }}</span>
                                                                                @endif

                                                                                @foreach ($rows['child'] as $keyz => $rowz)
                                                                                <p class="p-0 text-primary"><strong><i class="flaticon-questions-circular-button mr-3"></i> {{ $rowz['pertanyaan_detail_title'] }}</strong></p>

                                                                                @if ($rowz['pertanyaan_detail_pilihan'] == 'radio' || $rowz['pertanyaan_detail_pilihan'] == 'dropdown')
                                                                                <p class="p-0 mb-7"><i class="flaticon2-pen mr-3"></i> {{ $rowz['pertanyaan_bobot_label'] }}</p>
                                                                                @elseif ($rowz['pertanyaan_detail_pilihan'] == 'tanggal')
                                                                                <p class="p-0 mb-7"><i class="flaticon2-pen mr-3"></i> {!! Helper::customDateMember($rowz['value']) !!}</p>
                                                                                @elseif ($rowz['pertanyaan_detail_pilihan'] == 'upload')
                                                                                <p class="p-0 mb-7"><i class="flaticon2-pen mr-3"></i> {{ $rowz['value'] }} <a class="btn btn-sm btn-success font-weight-bolder mr-2" target="_blank" href="{{ url('uploads/memberfile/' . $rowz['value']) }} {{ $rowz['pertanyaan_bobot_label'] }}">Lihat Dokumen</a></p>
                                                                                @else
                                                                                <p class="p-0 mb-7"><i class="flaticon2-pen mr-3"></i> {{ $rowz['value'] }}</p>
                                                                                @endif

                                                                                @endforeach

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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection