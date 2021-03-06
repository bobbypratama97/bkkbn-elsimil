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
                    <div class="alert-text"><strong>Perhatian</strong><br />{{ Session::get( 'success' ) }}</div>
                </div>
                @endif

                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Widget
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data widget untuk kebutuhan kuisioner</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-inline mb-7" method="GET" action="{{ route('admin.widget.index') }}">
                            <div class="form-group mr-3">
                                <label for="name">Cari : </label>
                                <input type="search" name="name" value="{{ (isset($name)) ? $name : ""}}"  class="form-control form-control-sm ml-3" placeholder="Name, Dibuat Oleh" aria-controls="kt_datatable" _vkenabled="true">
                            </div>
                            <button type="submit" class="btn btn-success">Filter </button>
                        </form>
                        <table class="table table-bordered table-checkable table-responsive" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important; height: 100% !important;overflow-x:auto !important;display:block;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="73%">Widget</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Dibuat Oleh</th>
                                    <th width="7%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($widget) == 0)
                                <tr><td colspan="6" align="center">Data tidak ditemukan.</td></tr>
                                @endif
                                @foreach($widget as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{!! Helper::status($row->status) !!}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{!! Helper::customUser($row->nama) !!}</td>
                                    <td style="white-space: nowrap">
                                        @can('access', [\App\Widget::class, Auth::user()->role, 'edit'])
                                        <a href="{{ route('admin.widget.edit', $row->id) }}" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Ubah">
                                            <i class="flaticon2-edit"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="float-left">
                            @if (count($widget) > 1)
                                Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($widget) }} dari {{ $paginate->total() }} data
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
                        url: '{{ route("admin.kategori.delete") }}',
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
                                            window.location.href = '{{ route("admin.kategori.index") }}';
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
