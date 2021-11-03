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
                            <h3 class="card-label">Reporting - Hasil Kuesioner
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data hasil kuisioner member</span></h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Kuesioner :</label>
                                <select name="" class="form-control select2" id="kuesioner">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Provinsi</label>
                                <select name="" class="form-control select2">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Kabupaten</label>
                                <select name="" class="form-control select2">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Kecamatan</label>
                                <select name="" class="form-control select2">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Kelurahan</label>
                                <select name="" class="form-control select2">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>NIK</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-lg-3">
                                <label>Nama</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-lg-3">
                                <label>Gender</label>
                                <select name="" class="form-control select2">
                                    <option value="">Pilih</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <button type="button" class="btn btn-success btn-block mt-8"><i class="flaticon-search"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

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
