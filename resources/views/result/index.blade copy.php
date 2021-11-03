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
                            <h3 class="card-label">Hasil Kuesioner
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data hasil kuisioner member</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kuesioner</th>
                                    <th>Member</th>
                                    <th>Waktu Pelaksanaan</th>
                                    <th>Kode</th>
                                    <th>Nilai Member</th>
                                    <th>Label</th>
                                    <th>Ulasan Petugas</th>
                                    <th width="4%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $key => $row)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $row->kuis_title }}</td>
                                    <td>{!! Helper::customUser($row->nama) !!}<br /><small>{{ $row->kelurahan }}, {{ $row->kecamatan }}, {{ $row->kabupaten }}, {{ $row->provinsi }}</small></td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{{ $row->kuis_code }}</td>
                                    <td>{{ $row->member_kuis_nilai }} / {{ $row->kuis_max_nilai }}</td>
                                    <td>{{ $row->kuis_label }}</td>
                                    <td>{{ (!empty($row->komentar)) ? 'Sudah' : 'Belum' }}</td>
                                    <td>
                                        <a href="{{ route('admin.result.edit', $row->id) }}" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Detail Hasil Kuis">
                                            <i class="flaticon2-writing"></i>
                                        </a>
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
            //"sScrollX": "100%",
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
                        url: '{{ route('admin.kategori.delete') }}',
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
                                            window.location.href = '{{ route('admin.kategori.index') }}';
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
