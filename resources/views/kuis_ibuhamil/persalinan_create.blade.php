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
                            <button type="button" class="btn btn-sm btn-block" style="background-color: #1CC5BE; color: white; width: 75%">Tgl Pengisian : @php echo isset($data_kuesioner->created_at) ? ($data_kuesioner->created_at) : null; @endphp </button>
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
                            <button type="button" class="btn btn-sm btn-block" style="background-color: #1C7EC5; color: white; width: 75%">Tgl Update : @php echo isset($data_kuesioner->updated_at) ? ($data_kuesioner->updated_at) : null; @endphp </button>
                        </div>
                        <div class="col-lg-8">
                            <form action="{{route('admin.periodePersalinan-save',$id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="">1. Tanggal Persalinan</label>
                                    <input type="date" name="tanggal_persalinan" class="form-control" value="@php echo isset($data_kuesioner->tanggal_persalinan) ? ($data_kuesioner->tanggal_persalinan) : null; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="">2. KB Pasca Persalinan</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kb"  <?php   echo  isset($data_kuesioner->kb) && $data_kuesioner->kb == '1' ? 'checked':'' ?>   value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kb" <?php   echo  isset($data_kuesioner->kb) && $data_kuesioner->kb =='0' ? 'checked':'' ?>   value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <h3>Bayi</h3>
                                <div class="form-group">
                                    <label for="nama"> 1. Usia Janin Dalam Kandungan</label>
                                    <input type="number" class="number form-control" name="usia" value="@php echo isset($data_kuesioner->usia) ? ($data_kuesioner->usia) : null; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 2. Berat Lahir</label>
                                    <input type="number" class="number form-control" name="berat" value="@php echo isset($data_kuesioner->berat) ? ($data_kuesioner->berat) : null; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 3. Panjang Badan</label>
                                    <input type="number" class="number form-control" name="panjang_badan" value="@php echo isset($data_kuesioner->panjang_badan) ? ($data_kuesioner->panjang_badan) : null; @endphp">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 4. Jumlah Bayi</label>
                                    <input type="number" class="number form-control" name="jumlah" value="@php echo isset($data_kuesioner->jumlah) ? ($data_kuesioner->jumlah) : null; @endphp">
                                </div>
                                <button type="submit">Submit</button>
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
