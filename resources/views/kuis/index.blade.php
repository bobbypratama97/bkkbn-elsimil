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
                            <h3 class="card-label">Kuesioner 
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data kuesioner yang akan diisi oleh member di aplikasi</span></h3>
                        </div>
                        <div class="card-toolbar">
                            @can('access', [\App\Kuis::class, Auth::user()->role, 'sort'])
                            <a href="{{ route('admin.kuis.sort') }}" class="btn btn-success font-weight-bolder mr-3">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo6/dist/assets/media/svg/icons/Design/Flatten.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="9" cy="15" r="6" />
                                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                            </span>Sorting Kuesioner</a>
                            @endcan
                            @can('access', [\App\Kuis::class, Auth::user()->role, 'create'])
                            <a href="{{ route('admin.kuis.create') }}" class="btn btn-primary font-weight-bolder">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo6/dist/assets/media/svg/icons/Design/Flatten.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="9" cy="15" r="6" />
                                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                            </span>Tambah Kuesioner</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-inline mb-7" method="GET" action="{{ route('admin.kuis.index') }}">
                            <div class="form-group mr-3">
                                <label for="name">Cari : </label>
                                <input type="search" name="name" value="{{ (isset($name)) ? $name : ""}}"  class="form-control form-control-sm ml-3" placeholder="Judul, Gender, Dibuat Oleh" aria-controls="kt_datatable" _vkenabled="true">
                            </div>
                            <button type="submit" class="btn btn-success">Filter </button>
                        </form>
                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;display: block;overflow-x:auto !important;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th width="70%">Judul Kuesioner</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Dibuat Oleh</th>
                                    <th width="14%" >Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($kuis) == 0)
                                <tr>
                                    <td colspan="7" align="center">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                                @endif
                                @foreach($kuis as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{!! Helper::statusGender($row->gender) !!}</td>
                                    <td>
                                        @if ($row->apv == 'APV400' || $row->apv == 'APV500')
                                        @if (!empty($row->catatan))
                                        <small>
                                            <i class="flaticon-bell icon-md text-danger" data-toggle="popover" data-offset="20px 20px" data-placement="top" title="Keterangan" data-html="true" data-content="{{ $row->catatan }}"></i>
                                        </small>
                                        @endif
                                        @endif
                                        {!! Helper::approval($row->apv) !!}
                                    </td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{!! Helper::customUser($row->name) !!}</td>
                                    <td style="white-space: nowrap">
                                        @can('access', [\App\Kuis::class, Auth::user()->role, 'preview'])
                                        @if ($row->apv == 'APV400' || $row->apv == 'APV100')
                                        <a href="{{ route('admin.kuis.preview', $row->id) }}" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Ajukan Approval">
                                            <i class="flaticon-like"></i>
                                        </a>
                                        @endif
                                        @endcan
                                        @can('access', [\App\Kuis::class, Auth::user()->role, 'edit'])
                                        <a href="{{ route('admin.kuis.edit', $row->id) }}" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Ubah">
                                            <i class="flaticon2-writing"></i>
                                        </a>
                                        @endcan
                                        @can('access', [\App\Kuis::class, Auth::user()->role, 'show'])
                                        <a href="{{ route('admin.kuis.show', $row->id) }}" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Detail">
                                            <i class="flaticon2-menu-1"></i>
                                        </a>
                                        @endcan
                                        @can('access', [\App\Pertanyaan::class, Auth::user()->role, 'index'])
                                        <a href="{{ route('admin.pertanyaan.index', $row->id) }}" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Daftar Pertanyaan">
                                            <i class="flaticon-notepad"></i>
                                        </a>
                                        @endcan
                                        @can('access', [\App\Kuis::class, Auth::user()->role, 'delete'])
                                        <button class="btn btn-icon btn-sm btn-danger hapus" id="hapus"    title="Hapus" data-id="{{ $row->id }}">
                                            <i class="flaticon2-trash"></i>
                                       </button>
                                       @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="float-left">
                            @if (count($kuis) > 1)
                                Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($kuis) }} dari {{ $paginate->total() }} data
                            @else
                                Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} dari {{ $paginate->total() }} data
                            @endif
                        </div>
                        <div class="float-right">
                            {{ $paginate->appends($_GET)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    // $(document).ready(function() {
    //     var table = $('#kt_datatable').DataTable({
    //         "sScrollX": "100%",
    //         //"sScrollXInner": "110%",
    //         "bLengthChange": false,
    //         "ordering": false,
    //         "iDisplayLength": 10,
    //         "oLanguage": {
    //             "sSearch": "Cari : ",
    //             "oPaginate": {
    //                 "sFirst": "Hal. Pertama",
    //                 "sPrevious": "Sebelumnya",
    //                 "sNext": "Berikutnya",
    //                 "sLast": "Hal. Terakhir"
    //             }
    //         },
    //         "language": {
    //             "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
    //             "infoEmpty": "Menampilkan 0 dari _MAX_ data",
    //             "zeroRecords": "Tidak ada data",
    //             "sInfoFiltered":   "",
    //         },
    //         columnDefs: [
    //             { "width": "50px", "targets": [0] }
    //         ]
    //     });
    // });

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
</script>
@endpush

@endsection
