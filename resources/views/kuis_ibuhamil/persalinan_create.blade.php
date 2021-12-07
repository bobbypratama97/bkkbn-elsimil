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
                   <h4>Sesaat Setelah Hamil</h4>
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
                            <form action="{{route('admin.periodePersalinan-save',$id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">1. Tanggal Persalinan</p></label>
                                    <div class="input-group">
                                        <input type="date" name="tanggal_persalinan" class="form-control" value="@php echo isset($data_kuesioner->tanggal_persalinan) ? ($data_kuesioner->tanggal_persalinan) : null; @endphp">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">2. KB Pasca Persalinan</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kb"  <?php   echo  isset($data_kuesioner->kb) && $data_kuesioner->kb == 'Ya' ? 'checked':'' ?>   value="Ya" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                              Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kb" <?php   echo  isset($data_kuesioner->kb) && $data_kuesioner->kb == 'Tidak' ? 'checked':'' ?>   value="Tidak" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                              Tidak
                                            </label>
                                        </div>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                    <div class="row mt-5">
                                        <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;" style="display: block"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder">
                                                    <div style="margin-top: -8%">
                                                        <ol style="margin-left: 0px; margin-bottom: 10px">
                                                            <li>MOW (Metode Operasi Wanita)</li>
                                                            <li>IUD</li>
                                                            <li>Implan</li>
                                                            <li>Suntik KB</li>
                                                            <li>POP (Pil Progrestin Only)</li>
                                                            <li>Kondom</li>
                                                        </ol>
                                                    </div>
                                                </span>
                                        </div>
                                        <div class="col-sm-4">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                            <span class="text-muted mr-2 font-weight-bolder" style="text-align: left">Jika belum ber-KB maka perlu alert khusus </span>
                                            <span class="text-muted mr-2 font-weight-bolder" style="margin-left: 7%">agar menjadi perhatian petugas pendamping. </span>
                                        </div>
                                    </div>
                                    @endif

                                </div>
                                <h3>Bayi</h3>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">1. Usia Janin Dalam Kandungan</p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="usia_janin" value="@php echo isset($data_kuesioner->usia_janin) ? ($data_kuesioner->usia_janin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">minggu</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> 37 - 42 Minggu</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 37 Minggu atau > 42 Minggu </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">2. Berat Lahir</p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="berat_janin" value="@php echo isset($data_kuesioner->berat_janin) ? ($data_kuesioner->berat_janin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">gram</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> 2500 - 3900 gr</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 2500 gr atau > 3900 gr </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">3. Panjang Badan</p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="panjang_badan_janin" value="@php echo isset($data_kuesioner->panjang_badan_janin) ? ($data_kuesioner->panjang_badan_janin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">cm</span>
                                    </div>
                                    @if ($data_kuesioner->created_at == null)
                                        <div class="row mt-5">
                                            <div class="col-sm-3">
                                                <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> 48-53 cm</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span>
                                                <span class="text-muted mr-2 font-weight-bolder"> < 48 atau > 53 cm </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0">4. Jumlah Bayi</p></label>
                                    <div class="input-group">
                                      <input type="number" class="number form-control" name="jumlah_bayi" value="@php echo isset($data_kuesioner->jumlah_bayi) ? ($data_kuesioner->jumlah_bayi) : null; @endphp">
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
