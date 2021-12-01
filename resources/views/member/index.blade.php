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
                            <h3 class="card-label">Catin
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data catin</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-inline mb-7" method="GET" action="{{ route('admin.member.index') }}">
                            <div class="form-group mr-3">
                                <label for="email">Cari : </label>
                                <select name="s" class="form-control ml-3">
                                    <option value="">Pilih</option>
                                    <option value="all" {{ (isset($search) && $search == "all") ? "selected" : ""}}>Semua</option>
                                    <option value="m" {{ (isset($search) && $search == "m") ? "selected" : ""}}>Catin saya</option>
                                    <option value="h" {{ (isset($search) && $search == "h") ? "selected" : ""}}>Sudah punya petugas</option>
                                    <option value="nh" {{ (isset($search) && $search == "nh") ? "selected" : ""}}>Belum punya petugas</option>
                                </select>
                            </div>

                            <div class="form-group mr-3">
                                <label for="name">Nama Catin : </label>
                                <input type="search" name="name" value="{{ (isset($name)) ? $name : ""}}"  class="form-control form-control-sm ml-3" placeholder="" aria-controls="kt_datatable" _vkenabled="true">
                            </div>
                            <button type="submit" class="btn btn-success">Filter </button>
                        </form>

                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;overflow-x:auto !important;display:block;white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Pasangan</th>
                                    <th>No KTP</th>
                                    <th width="20%">Lokasi</th>
                                    <th>No Telp</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Petugas KB</th>
                                    <th width="14%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($member as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    <td>{{ (!empty($row['pasangan'])) ? $row['pasangan'] : '-' }}</td>
                                    <td>{!! Helper::decryptNik($row['no_ktp']) !!}</td>
                                    <td>{{ $row['kelurahan'] }}, {{ $row['kecamatan'] }}, {{ $row['kabupaten'] }}, {{ $row['provinsi']}}</td>
                                    <td>{{ $row['no_telp'] }}</td>
                                    <td>{{ Helper::jenisKelamin($row['gender']) }}</td>
                                    <td>{!! Helper::statusUser($row['is_active']) !!}</td>
                                    <td>{{ $row['created_at'] }}</td>
                                    <td>{{ (!empty($row['petugas'])) ? $row['petugas'] : '-' }}</td>
                                    <td class="text-right" width="14%">
                                        @if ($row['gender'] == 2)
                                            <a href="{{ route('admin.member.ibuhamil', $row['id']) }}" class="btn btn-icon btn-sm btn-primary"  title="Tambah Kuesioner Ibu Hamil" style="background-color: #EB30EF">
                                                <i class="flaticon2-notepad"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.member.result', $row['id']) }}" class="btn btn-icon btn-sm btn-primary"  title="Lihat Hasil Kuesioner">
                                            <i class="flaticon2-writing"></i>
                                        </a>
                                        @can('access', [\App\Member::class, Auth::user()->role, 'show'])
                                        <a href="{{ route('admin.member.show', $row['id']) }}" class="btn btn-icon btn-sm btn-success"   title="Detail">
                                            <i class="flaticon2-menu-1"></i>
                                        </a>
                                        @endcan
                                        @can('access', [\App\Member::class, Auth::user()->role, 'edit'])
                                        <a href="{{ route('admin.member.edit', $row['id']) }}" class="btn btn-icon btn-sm btn-warning"   title="Edit">
                                            <i class="flaticon2-menu-4"></i>
                                        </a>
                                        @endcan
                                        @if (empty($row['petugas_id']))
                                        <button class="btn btn-icon btn-sm btn-warning kelola" id="kelola"  title="Dampingi catin" data-id="{{ $row['id'] }}">
                                            <i class="flaticon-businesswoman"></i>
                                       </button>
                                       @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="float-left">
                    @if (count($member) > 1)
                        Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($member) }} dari {{ $paginate->total() }} data
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

    $('#kt_datatable tbody').on('click', '.kelola', function () {
        var id = $(this).attr('data-id');

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
                        data: {id : id, '_token': "{{ csrf_token() }}"},
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
                                                window.location.href = '{{ route('admin.member.index') }}';
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

</script>
@endpush

@endsection
