@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">

            @if ( Session::has( 'success' ))
            <div class="alert alert-custom alert-success" role="alert">
                <div class="alert-icon">
                    <i class="flaticon2-telegram-logo"></i>
                </div>
                <div class="alert-text"><strong>Perhatian</strong><br />{!! Session::get( 'success' ) !!}</div>
            </div>
            @endif

            <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap py-3">
                    <div class="card-title">
                        <h3 class="card-label">Data Ibu Hamil : {{$name}} </h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{route('admin.member.ibuhamil',$id)}}" class="btn btn-primary font-weight-bolder" style="background-color: #F64F61">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-body py-4">
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">Nama : {{$name}} </span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-website"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">No KTP :  {{$no_ktp}}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon-rotate"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">Gender : {{$gender}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card-body py-4">
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon2-user"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">Usia :  {{$umur}} Tahun</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon-calendar-with-a-clock-time-tools"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">Tempat / Tgl Lahir : {{$tempat_lahir}} , {{ \Carbon\Carbon::parse($tanggal_lahir)->format('d F Y')}}</span>
                                    </div>
                                </div>
                                <div class="form-group row my-0">
                                    <label class="col-1 col-form-label"><i class="flaticon-placeholder-3"></i></label>
                                    <div class="col-11">
                                        <span class="form-control-plaintext font-weight-bolder">Lokasi : {{$alamat}} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card card-custom">
                <div class="card-body">
                   <h4>Usia Hamil 12 Minggu</h4>
                    <p>Pengisian kuesioner ini dilakukan oleh Faskes / Posyandu.</p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                  @endif
                    <div class="row">
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-sm btn-block font-weight-boldest" style="background-color: #1CC5BE; color: white; width: 75%">Tgl Pengisian : @php echo isset($data_kuesioner->created_at) ? ($data_kuesioner->created_at) : null; @endphp </button>
                        </div>
                        <div class="col-lg-8">
                            @if ( Session::has( 'success' ))
                                <button type="button" class="btn btn-sm btn-block font-weight-boldest" style="background-color: #1CC5BE; color:white">Pengisian Kuesioner Berhasil</button>
                            @elseif ( $errors->any())
                                <button type="button" class="btn btn-sm btn-block font-weight-boldest" style="background-color: #F64F61; color:white">Pengisian Kuesioner Gagal</button>
                            @else
                                <button type="button" class="btn btn-sm btn-block font-weight-boldest" style="background-color: #1CC5BE; color:white">Silahkan Mengisi Kuesioner</button>
                            @endif
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1%">
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-sm btn-block font-weight-boldest" style="background-color: #1C7EC5; color: white; width: 75%">Tgl Update : @php echo isset($data_kuesioner->updated_at) ? ($data_kuesioner->updated_at) : null; @endphp </button>
                        </div>
                        <div class="col-lg-8">
                            <form action="{{route('admin.periode12minggu-save',$id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    @if (isset($data_kuesioner->berat_badan) && isset($data_kuesioner->tinggi_badan))
                                        @php
                                             $tinggiBadanMeter = $data_kuesioner->tinggi_badan / 100;
                                             $imtCalculation = $data_kuesioner->berat_badan / ($tinggiBadanMeter ^ 2);
                                        @endphp
                                        @if ($imtCalculation >= 19 && $imtCalculation <= 29)
                                            <label for="nama"><p class="font-weight-boldest m-0">1. Berat Badan</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($imtCalculation < 19 || $imtCalculation > 29)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 1. Berat Badan </p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0"> 1. Berat Badan</p></label>
                                    @endif
                                    <div class="input-group">
                                        <input type="number" class="text form-control" name="berat_badan" value="@php echo isset($data_kuesioner->berat_badan) ? ($data_kuesioner->berat_badan) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">kg</span>
                                    </div>
                                       @php
                                        if(isset($data_kuesioner)){
                                            $dataExisting = True;
                                        }else{
                                            $dataExisting = False;
                                        }
                                        @endphp
                                        @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Rumus IMT (19-29)</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Rumus IMT ( < 19 atau > 29) </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    @if (isset($data_kuesioner->tinggi_badan))
                                        @if ($data_kuesioner->tinggi_badan >= 145)
                                            <label for="nama"><p class="font-weight-boldest m-0">2. Tinggi Badan</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($data_kuesioner->tinggi_badan < 145)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 2. Tinggi Badan</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0">2. Tinggi Badan</p></label>
                                    @endif
                                    <div class="input-group">
                                        <input type="number" class="text form-control" name="tinggi_badan" value="@php echo isset($data_kuesioner->tinggi_badan) ? ($data_kuesioner->tinggi_badan) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">cm</span>
                                    </div>
                                    @php
                                    if(isset($data_kuesioner)){
                                        $dataExisting = True;
                                    }else{
                                        $dataExisting = False;
                                    }
                                    @endphp
                                    @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> >= 145 cm</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 145 cm </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    @if (isset($data_kuesioner->lingkar_lengan_atas))
                                        @if ($data_kuesioner->lingkar_lengan_atas >= 23.5)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 3. Lingkar Lengan Atas (LiLA)</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($data_kuesioner->lingkar_lengan_atas < 23.5)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 3. Lingkar Lengan Atas (LiLA)</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0"> 3. Lingkar Lengan Atas (LiLA)</p></label>
                                    @endif
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="lingkar_lengan_atas" value="@php echo isset($data_kuesioner->lingkar_lengan_atas) ? ($data_kuesioner->lingkar_lengan_atas) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">cm</span>
                                    </div>
                                    @php
                                        if(isset($data_kuesioner)){
                                            $dataExisting = True;
                                        }else{
                                            $dataExisting = False;
                                        }
                                        @endphp
                                    @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> >= 23,5 cm</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 23,5 cm </span>
                                            </div>
                                        </div>
                                       @endif
                                </div>
                                <div class="form-group">
                                    @if (isset($data_kuesioner->hemoglobin))
                                        @if ($data_kuesioner->hemoglobin >= 11)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 4. Kadar Hemoglobin (Hb)</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($data_kuesioner->hemoglobin < 11)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 4. Kadar Hemoglobin (Hb)</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0"> 4. Kadar Hemoglobin (Hb)</p></label>
                                    @endif
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="hemoglobin" value="@php echo isset($data_kuesioner->hemoglobin) ? ($data_kuesioner->hemoglobin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">gr/dl</span>
                                    </div>
                                    @php
                                    if(isset($data_kuesioner)){
                                        $dataExisting = True;
                                    }else{
                                        $dataExisting = False;
                                    }
                                    @endphp
                                    @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> >= 11 gr/dl</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 11 gr/dl </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    @if (isset($data_kuesioner->tensi_darah))
                                        @if ($data_kuesioner->tensi_darah <= 90)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 5. Tensi Darah (Rumus MAP)</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($data_kuesioner->tensi_darah > 90)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 5. Tensi Darah (Rumus MAP)</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0"> 5. Tensi Darah (Rumus MAP)</p></label>
                                    @endif
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="tensi_darah" value="@php echo isset($data_kuesioner->tensi_darah) ? ($data_kuesioner->tensi_darah) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">mmHg</span>
                                    </div>
                                    @php
                                        if(isset($data_kuesioner)){
                                            $dataExisting = True;
                                        }else{
                                            $dataExisting = False;
                                        }
                                    @endphp
                                    @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> <= 90 mmHg</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> > 90 mmHg </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    @if (isset($data_kuesioner->gula_darah_sewaktu))
                                        @if ($data_kuesioner->gula_darah_sewaktu >= 95 && $data_kuesioner->gula_darah_sewaktu <= 200)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 6. Gula Darah Sewaktu</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($data_kuesioner->gula_darah_sewaktu < 95 || $data_kuesioner->gula_darah_sewaktu > 200)
                                            <label for="nama"><p class="font-weight-boldest m-0"> 6. Gula Darah Sewaktu</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0">6. Gula Darah Sewaktu</p></label>
                                    @endif
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="gula_darah_sewaktu" value="@php echo isset($data_kuesioner->gula_darah_sewaktu) ? ($data_kuesioner->gula_darah_sewaktu) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">mg/dl</span>
                                    </div>
                                    @php
                                    if(isset($data_kuesioner)){
                                        $dataExisting = True;
                                    }else{
                                        $dataExisting = False;
                                    }
                                    @endphp
                                    @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> 95 - 200 mg/dl</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 95 mg/dl atau > 200 mg/dl </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    @if (isset($data_kuesioner->riwayat_sakit_kronik))
                                        @if ($data_kuesioner->riwayat_sakit_kronik == "Tidak Ada")
                                            <label for="nama"><p class="font-weight-boldest m-0"> 7. Riwayat Sakit Kronik</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#1CC574;
                                            color:white">
                                            Ideal
                                            </span>
                                        @elseif($data_kuesioner->riwayat_sakit_kronik == "Ada")
                                            <label for="nama"><p class="font-weight-boldest m-0"> 7. Riwayat Sakit Kronik</p></label>
                                            <span style=
                                            "margin-left: 1%; padding-left:1%;padding-right:1%;
                                            border-radius: 25px ;
                                            background:#F64F61;
                                            color:white">
                                            Berisiko
                                            </span>
                                        @endif
                                    @else
                                        <label for="nama"><p class="font-weight-boldest m-0">7. Riwayat Sakit Kronik</p></label>
                                    @endif
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="riwayat_sakit_kronik"  <?php echo  isset($data_kuesioner->riwayat_sakit_kronik) && $data_kuesioner->riwayat_sakit_kronik == 'Ada' ? 'checked':'' ?>   value="Ada" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ada
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="riwayat_sakit_kronik" <?php   echo  isset($data_kuesioner->riwayat_sakit_kronik) && $data_kuesioner->riwayat_sakit_kronik == 'Tidak Ada' ? 'checked':'' ?>  value="Tidak Ada" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak Ada
                                            </label>
                                        </div>
                                    </div>
                                    @php
                                    if(isset($data_kuesioner)){
                                        $dataExisting = True;
                                    }else{
                                        $dataExisting = False;
                                    }
                                    @endphp
                                    @if ($dataExisting == False)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Tidak Ada</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Ada </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-success btn-lg btn-block mt-6"><span class="font-weight-boldest">Simpan</span></button>
                            </form>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
</div>


@push('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
@endpush
@endsection
