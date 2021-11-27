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
                            <button type="button" class="btn btn-success btn-lg btn-block"><span class="font-weight-boldest">Tgl Pengisian : @php echo isset($data_kuesioner->created_at) ? ($data_kuesioner->created_at) : null; @endphp </button>
                        </div>
                        <div class="col-lg-8">
                            @if ( Session::has( 'success' ))
                                <button type="button" class="btn btn-sm btn-block" style="background-color: #1CC5BE; color:white">Pengisian Kuesioner Berhasil</button>
                            @elseif ( $errors->any())
                                <button type="button" class="btn btn-sm btn-block" style="background-color: #F64F61; color:white">Pengisian Kuesioner Gagal</button>
                            @else
                                <button type="button" class="btn btn-sm btn-block" style="background-color: #1CC5BE; color:white">Silahkan Mengisi Kuesioner</button>
                            @endif
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1%">
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-info btn-lg btn-block mb-5"><span class="font-weight-boldest">Tgl Update : @php echo isset($data_kuesioner->updated_at) ? ($data_kuesioner->updated_at) : null; @endphp </button>
                        </div>
                        <div class="col-lg-8">
                            <form action="{{route('admin.kontakawal-save',$id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="nama"> 1. Nama</label>
                                    <input type="text" class="text form-control" name="nama" value="@php echo isset($data_kuesioner->nama) ? ($data_kuesioner->nama) : $name; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 2. NIK</label>
                                    <input type="text" class="text form-control" name="nik" value="@php echo isset($data_kuesioner->nik) ? Helper::decryptNik($data_kuesioner->nik) : ""; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 3. Usia</label>
                                    <input type="number" class="number form-control" name="usia" value="@php echo isset($data_kuesioner->usia) ? ($data_kuesioner->usia) : $umur; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 4. Alamat</label>
                                    <input type="text" class="text form-control" name="alamat" value=@php echo isset($data_kuesioner->alamat) ? ($data_kuesioner->alamat) : $alamat; @endphp>
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 5. Jumlah Anak</label>
                                    <input type="number" class="number form-control" name="jumlah_anak" value="@php echo isset($data_kuesioner->jumlah_anak) ? ($data_kuesioner->jumlah_anak) : null; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 6. Usia Anak Terakhir</label>
                                    <input type="number" class="number form-control" name="usia_anak_terakhir" value="@php echo isset($data_kuesioner->usia_anak_terakhir) ? ($data_kuesioner->usia_anak_terakhir) : null; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="">7. Anak Stunting</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="anak_stunting"  <?php echo  isset($data_kuesioner->usia_anak_terakhir) && $data_kuesioner->anak_stunting =='1' ? 'checked':'' ?>   value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="anak_stunting" <?php   echo  isset($data_kuesioner->usia_anak_terakhir) && $data_kuesioner->anak_stunting =='0' ? 'checked':'' ?>  value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">8. Hari Pertama Haid Terakhir</label>
                                    <input type="date" name="hari_pertama_haid_terakhir" class="form-control" value="@php echo isset($data_kuesioner->hari_pertama_haid_terakhir) ? ($data_kuesioner->hari_pertama_haid_terakhir) : date("Y-m-d"); @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="">9. Sumber Air Bersih</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_air_bersih"  <?php   echo  isset($data_kuesioner->sumber_air_bersih) && $data_kuesioner->sumber_air_bersih =='1' ? 'checked':'' ?>   value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_air_bersih" <?php   echo  isset($data_kuesioner->sumber_air_bersih) && $data_kuesioner->sumber_air_bersih =='0' ? 'checked':'' ?>   value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">10. Rumah Layak Huni</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rumah_layak_huni"  <?php   echo  isset($data_kuesioner->rumah_layak_huni) && $data_kuesioner->rumah_layak_huni =='1' ? 'checked':'' ?> value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rumah_layak_huni"<?php   echo  isset($data_kuesioner->rumah_layak_huni) && $data_kuesioner->rumah_layak_huni =='0' ? 'checked':'' ?> value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">11. Bansos</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bansos" <?php   echo  isset($data_kuesioner->bansos) && $data_kuesioner->bansos == '1' ? 'checked':'' ?> value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bansos"  <?php   echo  isset($data_kuesioner->bansos) && $data_kuesioner->bansos =='0' ? 'checked':'' ?> value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
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
