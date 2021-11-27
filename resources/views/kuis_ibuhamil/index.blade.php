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
                        <a href="{{route('admin.member.index')}}" class="btn btn-primary font-weight-bolder" style="background-color: #F64F61">Kembali</a>
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
                <div class="card-header flex-wrap py-3">
                    <div class="card-title">
                        <p class="card-label">Pengisian kuesioner sesuai periode kehamilan atau pasca bersalin </p> <br>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-1.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Kontak Awal</a>
                                            @if ($kuesionerData[0]['created_at'] != null)
                                                <a href="{{route('admin.kontakawal-create',$id)}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                <a href="{{route('admin.kontakawal-create',$id)}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 12 Minggu</a>
                                            @if ($kuesionerData[1]['created_at'] != null)
                                                <a href="{{route('admin.periode12minggu-create',$id)}}"class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                <a href="{{route('admin.periode12minggu-create',$id)}}"class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 16 Minggu</a>
                                            @if ($kuesionerData[2]['created_at'] != null)
                                                <a href="{{route('admin.periode16minggu-create',$id)}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                <a href="{{route('admin.periode16minggu-create',$id)}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 20 Minggu</a>
                                            @if ($kuesionerData[3]['created_at'] != null)
                                            @php $periode = 20 @endphp
                                            <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                            @php $periode = 20 @endphp
                                            <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}"  class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 24 Minggu</a>
                                            @if ($kuesionerData[4]['created_at'] != null)
                                                @php $periode = 24 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                @php $periode = 24 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 28 Minggu</a>
                                            @if ($kuesionerData[5]['created_at'] != null)
                                                @php $periode = 28 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                @php $periode = 28 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 32 Minggu</a>
                                            @if ($kuesionerData[6]['created_at'] != null)
                                                @php $periode = 32 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                @php $periode = 32 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-2.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Usia Hamil 36 Minggu</a>
                                            @if ($kuesionerData[7]['created_at'] != null)
                                                @php $periode = 36 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                @php $periode = 36 @endphp
                                                <a href="{{route('admin.periodeIbuJanin-create',['id' => $id, 'periode' => $periode])}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-3.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Sesaat Setelah Persalinan</a>
                                            @if ($kuesionerData[8]['created_at'] != null)
                                                <a href="{{route('admin.periodePersalinan-create',['id' => $id])}}" class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                            <a href="{{route('admin.periodePersalinan-create',['id' => $id])}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card card-custom card-shadowless gutter-b" style="background-color: #E8EFFF">
                                <div class="card-body my-3" >
                                    <div class="row">
                                        <div class="col-lg-3 col-md-12">
                                            <img src="{{url('/assets/media/svg/illustrations/periode-3.png')}}" alt="..." class="img-thumbnail">
                                        </div>
                                        <div class="col-lg-9 col-md-12" style="margin-top: 1%">
                                            <a class="card-title font-weight-bolder text-hover-state-dark mb-4 d-block" style="color: black">Pasca Salin Akhir Masa Nifas</a>
                                            @if ($kuesionerData[9]['created_at'] != null)
                                                <a href="{{route('admin.periodeNifas-create',['id' => $id])}}"  class="btn btn-sm btn-block" style="background-color: #1CC5BE ; color: white">
                                                    <span>Sudah Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-check-mark" style="color: white"></i></label>
                                                </a>
                                            @else
                                                <a href="{{route('admin.periodeNifas-create',['id' => $id])}}" class="btn btn-sm btn-block" style="background-color: #FFFFFF ; color: #999999">
                                                    <span>Belum Mengisi Kuesioner </span>
                                                    <label class="col-1 col-form-label"><i class="flaticon2-delete"></i></label>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
