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
                            <h3 class="card-label">Inbox 
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan chat petugas KB dan catin</span></h3>
                        </div>
                    </div>
                    <div class="card-body">

                        <form class="form-inline mb-7" method="GET" action="{{ route('admin.chat.search') }}">
                            <div class="form-group mr-3">
                                <label for="email">Cari : </label>
                                <select name="search" class="form-control ml-3">
                                    <option value="">Pilih</option>
                                    <option value="all" {{ (isset($selected) && $selected == "all") ? 'selected' : '' }}>Semua</option>
                                    <option value="mine" {{ (isset($selected) && $selected == "mine") ? 'selected' : '' }}>Catin Saya</option>
                                    <option value="other" {{ (isset($selected) && $selected == "other") ? 'selected' : '' }}>Catin Petugas Lain</option>
                                    <option value="nh" {{ (isset($selected) && $selected == "nh") ? 'selected' : '' }}>Belum Punya Petugas</option>
                                </select>
                            </div>
                            <div class="form-group mr-3">
                            <!-- <button type="submit" class="btn btn-success">Filter</button> -->
                            </div>
                            <div class="form-group mr-3">
                                <input value="{{ app('request')->input('keyword') }}" name="keyword" width="100%" class="form-control" placeholder="Cari berdasarkan Nama, Pesan, Status, Petugas">
                            </div>
                            <button type="submit" class="btn btn-success">Search</button>
                        </form>

                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Thumbnail</th>
                                    <th>Nama</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                    <th>Petugas</th>
                                    <th width="30">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($list))
                                <tr>
                                    <td colspan="7" align="center">Data tidak ditemukan</td>
                                </tr>
                                @endif
                                @foreach($list as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td width="50">
                                        <img src="{{ $row->gambar }}" height="25" class="d-block mx-auto">
                                    </td>
                                    <td>{{ $row->name }}<br /><small>{{ $row->lokasi }}</small></td>
                                    <td>{{ $row->message }}<br /><small>{{ $row->waktu }}</small></td>
                                    <td class="text-center"><button class="btn btn-{{ $row->background }} font-weight-bolder unclick">{{ $row->label }}</button></td>
                                    <td>{!! Helper::customUser($row->petugas) !!}</td>
                                    <td>
                                        @can('access', [\App\Chat::class, Auth::user()->role, 'detail'])
                                        @if (Auth::user()->role == 1)
                                        <a href="{{ route('admin.chat.show', $row->chatid) }}" class="btn btn-icon btn-sm btn-info" title="Lihat Percakapan"><i class="flaticon2-talk"></i></a>
                                        @elseif ($row->header_responder_id == Auth::id())
                                        <a href="{{ route('admin.chat.show', $row->chatid) }}" class="btn btn-icon btn-sm btn-info" title="Lihat Percakapan"><i class="flaticon2-talk"></i></a>
                                        @elseif (!empty($row->header_responder_id) && $row->header_responder_id != Auth::id())
                                        <!-- <a href="{{ route('admin.chat.show', $row->chatid) }}" class="btn btn-icon btn-sm btn-info"  title="Lihat Percakapan"><i class="flaticon2-talk"></i></a> -->
                                        <a href="#" class="btn btn-icon btn-sm btn-default otherresp"  title="Lihat Percakapan"><i class="flaticon2-talk"></i></a>
                                        @else
                                        <button class="flaticon2-talk btn btn-icon btn-sm btn-info check"  title="Lihat Percakapan" data-id="{{ $row->id }}" data-chatid="{{ $row->chatid }}">
                                        </button>
                                        @endif
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="float-left">
                            @if (count($list) > 1)
                                Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($list) }} dari {{ $paginate->total() }} data
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
    //         searching: false,
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

    $('#kt_datatable tbody').on('click', '.check', function () {
        var id = $(this).attr('data-id');
        var chatid = $(this).attr('data-chatid');

        bootbox.confirm({
            title: 'Perhatian',
            message: "<p class='text-center'>Apakah Anda akan mendampingi catin ini ?</p>",
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
                        url: '{{ route('admin.member.kelola') }}',
                        type: 'POST',
                        data: {id : id, chatid: chatid, action: 'chat', '_token': "{{ csrf_token() }}"},
                        dataType: 'json',
                        success: function( data ) {
                            $.preloader.stop();

                            if (data.count == '0') {
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
                                                //window.location.href = '{{ route('admin.member.index') }}';
                                            }
                                        }
                                    }
                                });
                            } else {
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
                                                window.location.href = data.url;
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    })
                }
            }
        });
    });

    $('#kt_datatable tbody').on('click', '.otherresp', function () {
        var id = $(this).attr('data-id');
        var chatid = $(this).attr('data-chatid');

        bootbox.dialog({
            title: 'Perhatian',
            centerVertical: true,
            closeButton: false,
            message: "Sudah punya pendamping, tidak dapat melihat.",
            buttons: {
                ok: {
                    label: "OK",
                    className: 'btn-info',
                    callback: function() {
                        // window.location.href = data.url;
                    }
                }
            }
        });
    });
</script>
@endpush

@endsection
