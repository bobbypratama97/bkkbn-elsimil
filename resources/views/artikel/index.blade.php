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
                            <h3 class="card-label">Artikel 
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini berisi data artikel yang akan ditampilkan di aplikasi</span></h3>
                        </div>
                        @can('access', [\App\Artikel::class, Auth::user()->role, 'create'])
                        <div class="card-toolbar">
                            <a href="{{ route('admin.artikel.create') }}" class="btn btn-primary font-weight-bolder">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:/metronic/theme/html/demo6/dist/assets/media/svg/icons/Design/Flatten.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="9" cy="15" r="6" />
                                        <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                            </span>Tambah Artikel</a>
                        </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <form class="form-inline mb-7" method="GET" action="{{ route('admin.artikel.index') }}">
                            <div class="form-group mr-3">
                                <label for="name">Cari : </label>
                                <input type="search" name="name" value="{{ (isset($name)) ? $name : ""}}"  class="form-control form-control-sm ml-3" placeholder="Judu;, Kategori, Dibuat Oleh" aria-controls="kt_datatable" _vkenabled="true">
                            </div>
                            <button type="submit" class="btn btn-success">Filter </button>
                        </form>
                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important; height: 100% !important;overflow-x:auto !important;display: block;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Thumbnail</th>
                                    <th>Judul Artikel</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Dibuat Oleh</th>
                                    <th width="9%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($news) == 0)
                                <tr><td colspan="8" align="center">Data tidak ditemukan.</td></tr>
                                @endif
                                @foreach($news as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td width="50">
                                        <img src="{{ URL::to('/') }}/uploads/artikel/thumbnail/sm/sm_{{ $row->thumbnail }}" height="25" class="d-block mx-auto" alt="Gambar_artikel_{{ substr($row->title, 0, 10) }}">
                                    </td>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->parent }}</td>
                                    <td>{!! Helper::status($row->status) !!}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{!! Helper::customUser($row->nama) !!}</td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        @can('access', [\App\Artikel::class, Auth::user()->role, 'edit'])
                                        <a href="{{ route('admin.artikel.edit', $row->id) }}" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Ubah">
                                            <i class="flaticon2-writing"></i>
                                        </a>
                                        @endcan
                                        @can('access', [\App\Artikel::class, Auth::user()->role, 'show'])
                                        <a href="{{ route('admin.artikel.show', $row->id) }}" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Detail">
                                            <i class="flaticon2-menu-1"></i>
                                        </a>
                                        @endcan
                                        @can('access', [\App\Artikel::class, Auth::user()->role, 'delete'])
                                        <button class="btn btn-icon btn-sm btn-danger hapus" id="hapus"  data-toggle="tooltip"
                                                data-placement="top" title="Hapus" data-id="{{ $row->id }}">
                                            <i class="flaticon2-trash"></i>
                                       </button>
                                       @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="float-left">
                            @if (count($news) > 1)
                                Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($news) }} dari {{ $paginate->total() }} data
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
                        url: '{{ route("admin.artikel.delete") }}',
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
                                            window.location.href = '{{ route("admin.artikel.index") }}';
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
