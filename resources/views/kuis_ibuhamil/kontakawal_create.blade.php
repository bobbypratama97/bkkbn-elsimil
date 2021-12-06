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
                   <h4>Kontak Awal</h4>
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
                            <form action="{{route('admin.kontakawal-save',$id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 1. Nama</p></label>
                                    <input type="text" class="text form-control" name="nama" value="@php echo isset($data_kuesioner->nama) ? ($data_kuesioner->nama) : $name; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 2. NIK</p></label>
                                    <input type="text" class="text form-control" name="nik" value="@php echo isset($data_kuesioner->nik) ? Helper::decryptNik($data_kuesioner->nik) : ""; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 3. Usia</p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="usia" value="@php echo isset($data_kuesioner->usia) ? ($data_kuesioner->usia) : $umur; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">Tahun</span>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder">20-35</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> < 20 atau > 35 </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 4. Alamat</p></label>
                                    <input type="text" class="text form-control" name="alamat" value=@php echo isset($data_kuesioner->alamat) ? ($data_kuesioner->alamat) : $alamat; @endphp>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 5. Jumlah Anak</p></label>
                                    <input type="number" class="number form-control" name="jumlah_anak" value="@php echo isset($data_kuesioner->jumlah_anak) ? ($data_kuesioner->jumlah_anak) : null; @endphp">
                                    
                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder">0/1/2</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> > 2</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 6. Usia Anak Terakhir</p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="usia_anak_terakhir" value="@php echo isset($data_kuesioner->usia_anak_terakhir) ? ($data_kuesioner->usia_anak_terakhir) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">Tahun</span>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> >= 4</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> < 4</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><p class="font-weight-boldest m-0">7. Memiliki Anak Stunting</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="anak_stunting"  <?php echo  isset($data_kuesioner->usia_anak_terakhir) && $data_kuesioner->anak_stunting == 'Ya' ? 'checked':'' ?>   value="Ya" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="anak_stunting" <?php   echo  isset($data_kuesioner->usia_anak_terakhir) && $data_kuesioner->anak_stunting == 'Tidak' ? 'checked':'' ?>  value="Tidak" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> TIDAK</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> YA</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><p class="font-weight-boldest m-0">8. Hari Pertama Haid Terakhir</p></label>
                                    <input type="date" name="hari_pertama_haid_terakhir" class="form-control" value="@php echo isset($data_kuesioner->hari_pertama_haid_terakhir) ? ($data_kuesioner->hari_pertama_haid_terakhir) : date("Y-m-d"); @endphp">
                                </div>
                                <div class="form-group">

                                    <label for=""><p class="font-weight-boldest m-0">9. Memiliki Sumber Air Bersih</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sumber_air_bersih"  <?php   echo  isset($data_kuesioner->sumber_air_bersih) && $data_kuesioner->sumber_air_bersih =='Ya' ? 'checked':'' ?>   value="Ya" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sumber_air_bersih" <?php   echo  isset($data_kuesioner->sumber_air_bersih) && $data_kuesioner->sumber_air_bersih == 'Tidak' ? 'checked':'' ?>   value="Tidak" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> YA</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> TIDAK </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><p class="font-weight-boldest m-0">10. Memiliki Jamban Sehat</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jamban_sehat"  <?php   echo  isset($data_kuesioner->jamban_sehat) && $data_kuesioner->jamban_sehat == 'Ya' ? 'checked':'' ?>   value="Ya" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jamban_sehat" <?php   echo  isset($data_kuesioner->jamban_sehat) && $data_kuesioner->jamban_sehat == 'Tidak' ? 'checked':'' ?>  value="Tidak" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> YA</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> TIDAK </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><p class="font-weight-boldest m-0">11. Rumah Layak Huni</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rumah_layak_huni"  <?php   echo  isset($data_kuesioner->rumah_layak_huni) && $data_kuesioner->rumah_layak_huni == 'Ya' ? 'checked':'' ?> value="Ya" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rumah_layak_huni"<?php   echo  isset($data_kuesioner->rumah_layak_huni) && $data_kuesioner->rumah_layak_huni == 'Tidak' ? 'checked':'' ?> value="Tidak" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> YA</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> TIDAK </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><p class="font-weight-boldest m-0">12. Menerima Bansos</p></label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bansos" <?php   echo  isset($data_kuesioner->bansos) && $data_kuesioner->bansos == 'Ya' ? 'checked':'' ?> value="Ya" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ya
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bansos"  <?php   echo  isset($data_kuesioner->bansos) && $data_kuesioner->bansos == 'Tidak' ? 'checked':'' ?> value="Tidak" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-2">
                                            <span class="badge p-2 mr-2" style="background-color: #1CC574;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> TIDAK</span>
                                        </div>

                                        <div class="col-sm-3">
                                            <span class="badge p-2 mr-2" style="background-color: #F64F61;"> </span> 
                                            <span class="text-muted mr-2 font-weight-bolder"> YA </span>
                                        </div>
                                    </div>
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
