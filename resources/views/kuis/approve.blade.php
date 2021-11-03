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
                            <h3 class="card-label">Approval 
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data kuesioner yang membutuhkan approval</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Kuesioner</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Diproses Oleh</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kuis as $key => $row)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{!! Helper::statusGender($row->gender) !!}</td>
                                    <td>{!! Helper::approval($row->apv) !!}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{!! Helper::customUser($row->nama) !!}</td>
                                    <td>{!! Helper::customUser($row->proceed_name) !!}</td>
                                    <td>
                                        @can('access', [\App\Kuis::class, Auth::user()->role, 'review'])
                                        <a href="{{ route('admin.kuis.review', $row->id) }}" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Detail">
                                            <i class="flaticon2-menu-1"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('script')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
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
</script>
@endpush

@endsection
