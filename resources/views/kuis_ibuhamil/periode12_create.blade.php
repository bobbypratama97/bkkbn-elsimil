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
                                    <label for="nama"><p class="font-weight-boldest m-0">1. Berat Badan </p></label>
                                    <div class="input-group">
                                        <input type="number" class="text form-control" name="berat_badan" value="@php echo isset($data_kuesioner->berat_badan) ? ($data_kuesioner->berat_badan) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">kg</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 2. Tinggi Badan </p> </label>
                                    <div class="input-group">
                                        <input type="number" class="text form-control" name="tinggi_badan" value="@php echo isset($data_kuesioner->tinggi_badan) ? ($data_kuesioner->tinggi_badan) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">cm</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 3. Lingkar Lengan Atas </p></label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="lingkar_lengan_atas" value="@php echo isset($data_kuesioner->lingkar_lengan_atas) ? ($data_kuesioner->lingkar_lengan_atas) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">cm</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 4. Hemoglobin </p> </label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="hemoglobin" value="@php echo isset($data_kuesioner->hemoglobin) ? ($data_kuesioner->hemoglobin) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">gr/dl</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 5. Tensi Darah </p> </label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="tensi_darah" value="@php echo isset($data_kuesioner->tensi_darah) ? ($data_kuesioner->tensi_darah) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">mmHg</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nama"><p class="font-weight-boldest m-0"> 6. Gula Darah </p> </label>
                                    <div class="input-group">
                                        <input type="number" class="number form-control" name="gula_darah" value="@php echo isset($data_kuesioner->gula_darah) ? ($data_kuesioner->gula_darah) : null; @endphp">
                                        <span class="input-group-text rounded-0 bg-white font-weight-boldest">mg/dl</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""><p class="font-weight-boldest m-0">7. Riwayat Sakit Kronik </p> </label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="riwayat_sakit_kronik"  <?php echo  isset($data_kuesioner->riwayat_sakit_kronik) && $data_kuesioner->riwayat_sakit_kronik =='1' ? 'checked':'' ?>   value="1" id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                            Ada
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="riwayat_sakit_kronik" <?php   echo  isset($data_kuesioner->riwayat_sakit_kronik) && $data_kuesioner->riwayat_sakit_kronik =='0' ? 'checked':'' ?>  value="0" id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                            Tidak Ada
                                            </label>
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
