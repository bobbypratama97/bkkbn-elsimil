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
                   <h4>Usia Hamil {{$periode}} Minggu</h4>
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
                            <form action="{{route('admin.periodeIbuJanin-save',['id' => $id, 'periode' => $periode])}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <h3>Ibu Hamil</h3> <br>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">1. Kenaikan Berat Badan </p></label>
                                    <div class="input-group">
                                        <input type="number" class="text form-control" name="kenaikan_berat_badan" value="@php echo isset($data_kuesioner->kenaikan_berat_badan) ? ($data_kuesioner->kenaikan_berat_badan) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">kg</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">2. Hemoglobin </p></label>
                                    <div class="input-group">
                                        <input type="number" class="text form-control" name="hemoglobin" value="@php echo isset($data_kuesioner->hemoglobin) ? ($data_kuesioner->hemoglobin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">gr/dl</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> >= 11 gr/dl </span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 11 gr/dl </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">3. Tensi Darah </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="tensi_darah" value="@php echo isset($data_kuesioner->tensi_darah) ? ($data_kuesioner->tensi_darah) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">mmHg</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
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
                                    <label for="nama"><p class="font-weight-boldest m-0">4. Gula Darah </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="gula_darah" value="@php echo isset($data_kuesioner->gula_darah) ? ($data_kuesioner->gula_darah) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">mg/dl</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                    <div class="row mt-5">
                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                            <span class="text-muted mr-2 font-weight-bolder"> 95 - 200 mg/dl </span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                            <span class="text-muted mr-2 font-weight-bolder"> < 95 mg/dl atau > 200 mg/dl</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">5. Proteinuria </p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="proteinuria"  <?php echo  isset($data_kuesioner->proteinuria) && $data_kuesioner->proteinuria == 'Positif' ? 'checked':'' ?>   value="Positif" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                              Positif
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="proteinuria" <?php   echo  isset($data_kuesioner->proteinuria) && $data_kuesioner->proteinuria == 'Negatif' ? 'checked':'' ?>  value="Negatif" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                             Negatif
                                            </label>
                                        </div>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Negatif </span>
                                            </div>

                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Positif </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <h3>Janin</h3><br>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">6. Denyut Jantung </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="denyut_jantung" value="@php echo isset($data_kuesioner->denyut_jantung) ? ($data_kuesioner->denyut_jantung) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">bpm</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> 100 - 160 / menit</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 100 kali / menit atau > 160 / menit</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">7. Tinggi Fundus Uteri </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="tinggi_fundus_uteri" value="@php echo isset($data_kuesioner->tinggi_fundus_uteri) ? ($data_kuesioner->tinggi_fundus_uteri) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">cm</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                @if($periode == 20)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 17-23 cm </span>
                                                @elseif($periode == 24)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 20-26 cm </span>
                                                @elseif($periode == 28)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 24-30 cm </span>
                                                @elseif($periode == 32)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 27-33 cm </span>
                                                @elseif($periode == 36)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 31-37 cm </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                @if($periode == 20)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 17 cm atau > 23 cm</span>
                                                @elseif($periode == 24)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 20 cm atau > 26 cm</span>
                                                @elseif($periode == 28)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 24 cm atau > 30 cm</span>
                                                @elseif($periode == 32)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 27 cm atau > 33 cm</span>
                                                @elseif($periode == 36)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 31 cm atau > 37 cm</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">8. Taksiran Berat Janin </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="taksiran_berat_janin" value="@php echo isset($data_kuesioner->taksiran_berat_janin) ? ($data_kuesioner->taksiran_berat_janin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">gr</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                @if($periode == 20)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 300 - 325 gr </span>
                                                @elseif($periode == 24)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 550 - 685 gr </span>
                                                @elseif($periode == 28)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 1000 - 1150 gr </span>
                                                @elseif($periode == 32)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 1610 - 1810 gr </span>
                                                @elseif($periode == 36)
                                                    <span class="text-muted mr-2 font-weight-bolder"> 2500 - 2690 gr </span>
                                                @endif
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                @if($periode == 20)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 300 gr atau > 325 gr </span>
                                                @elseif($periode == 24)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 550 gr atau > 685 gr </span>
                                                @elseif($periode == 28)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 1000 gr atau > 1150 gr </span>
                                                @elseif($periode == 32)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 1610 gr atau > 1810 gr </span>
                                                @elseif($periode == 36)
                                                    <span class="text-muted mr-2 font-weight-bolder"> < 2500 gr atau > 2690 gr </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">9. Gerak Janin</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gerak_janin"  <?php echo  isset($data_kuesioner->gerak_janin) && $data_kuesioner->gerak_janin == 'Positif' ? 'checked':'' ?>   value="Positif" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                             Positif
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gerak_janin" <?php   echo  isset($data_kuesioner->gerak_janin) && $data_kuesioner->gerak_janin == 'Negatif' ? 'checked':'' ?>  value="Negatif" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                              Negatif
                                            </label>
                                        </div>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Positif </span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> Negatif </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">10. Jumlah Janin </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="jumlah_janin" value="@php echo isset($data_kuesioner->jumlah_janin) ? ($data_kuesioner->jumlah_janin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest"></span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> 1 </span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> > 1 </span>
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
