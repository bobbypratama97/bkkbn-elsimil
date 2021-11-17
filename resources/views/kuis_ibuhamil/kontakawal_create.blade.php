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
                        <a href="{{url()->previous()}}" class="btn btn-primary font-weight-bolder" style="background-color: #F64F61">Kembali</a>
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
                            <button type="button" class="btn btn-sm btn-block" style="background-color: #1CC5BE; color: white; width: 75%">Tgl Pengisian : </button>
                        </div>
                        <div class="col-lg-8">
                            <button type="button" class="btn btn-sm btn-block" style="background-color: #F64F61; color:white">Pengisian Kuesioner Gagal</button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1%">
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-sm btn-block" style="background-color: #1C7EC5; color: white; width: 75%">Tgl Update : </button>
                        </div>
                        <div class="col-lg-8">
                            <form action="{{route('admin.kontakawal-save',$id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="nama"> 1. Nama</label>
                                    <input type="text" class="text form-control" name="nama">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 2. NIK</label>
                                    <input type="text" class="text form-control" name="nik">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 3. Usia</label>
                                    <input type="number" class="number form-control" name="usia">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 4. Alamat</label>
                                    <input type="text" class="text form-control" name="alamat">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 5. Jumlah Anak</label>
                                    <input type="number" class="number form-control" name="jumlah_anak">
                                </div>
                                <div class="form-group">
                                    <label for="nama"> 6. Usia Anak Terakhir</label>
                                    <input type="number" class="number form-control" name="usia_anak_terakhir">
                                </div>
                                <div class="form-group">
                                    <label for="">7. Anak Stunting</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="anak_stunting" value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="anak_stunting" value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">8. Hari Pertama Haid Terakhir</label>
                                    <input type="date" name="hari_pertama_haid_terakhir" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">9. Sumber Air Bersih</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_air_bersih" value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sumber_air_bersih" value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">10. Rumah Layak Huni</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rumah_layak_huni" value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rumah_layak_huni" value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">11. Bansos</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bansos" value="1" id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                          Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bansos" value="0" id="flexRadioDefault2">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                          Tidak
                                        </label>
                                    </div>
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
</div>


@push('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

{{-- <script type="text/javascript">
    $(document).ready(function() {
        var table = $('#kt_datatable').DataTable({
            "sScrollX": "100%",
            //"sScrollXInner": "110%",
            "bLengthChange": false,
            "ordering": false,
            "iDisplayLength": 10,
            "oLanguage": {
                "sSearch": "Cari : ",
                "oPaginate": {
                    "sFirst": "Hal. Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext": "Berikutnya",
                    "sLast": "Hal. Terakhir"
                }
            },
            "language": {
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 dari _MAX_ data",
                "zeroRecords": "Tidak ada data",
                "sInfoFiltered":   "",
            },
            columnDefs: [
                { "width": "50px", "targets": [0] }
            ]
        });
    });

    $('#kt_datatable tbody').on('click', '.hapus', function () {
        var id = $(this).attr('data-id');

        bootbox.confirm({
            title: 'Perhatian',
            message: "<p class='text-center'>Apakah Anda yakin menghapus data ini ?</p>",
            centerVertical: true,
            closeButton: false,
            buttons: {
                confirm: { label: 'Yakin', className: 'btn-success' },
                cancel: { label: 'Batalkan', className: 'btn-danger' }
            },
            callback: function (result) {
                if (result == true) {
                    $.preloader.start({
                        modal:true,
                        src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                    });

                    $.ajax({
                        url: '{{ route('admin.kuis.delete') }}',
                        type: 'POST',
                        data: {id : id, '_token': "{{ csrf_token() }}"},
                        dataType: 'json',
                        success: function( data ) {
                            $.preloader.stop();
                            bootbox.dialog({
                                title: 'Perhatian',
                                centerVertical: true,
                                closeButton: false,
                                message: "<p class='text-center'>" + data.message + "</p>",
                                buttons: {
                                    ok: {
                                        label: "OK",
                                        className: 'btn-info',
                                        callback: function() {
                                            window.location.href = '{{ route('admin.kuis.index') }}';
                                        }
                                    }
                                }
                            });
                        }
                    })
                }
            }
        });
    });
</script> --}}
@endpush

@endsection
