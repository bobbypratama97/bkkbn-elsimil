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
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data hasil kuisioner catin</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-inline mb-7" method="GET" action="{{ route('admin.repkuis.detail') }}">
                            <div class="form-group mr-3">
                                <label for="email">Cari : </label>
                                <input type="hidden" name="curl" value="{{url()->full()}}">
                                <select name="search" class="form-control ml-3">
                                    <option value="">Pilih</option>
                                    <option value="all" {{ (isset($selected) && $selected == "all") ? "selected" : ""}}>Semua</option>
                                    <option value="mine" {{ (isset($selected) && $selected == "mine") ? "selected" : ""}}>Catin Saya</option>
                                    <option value="other" {{ (isset($selected) && $selected == "other") ? "selected" : ""}}>Catin Petugas Lain</option>
                                </select>
                            </div>
                            <!-- <button type="submit" class="btn btn-success">Filter</button> -->
                            <div class="form-group mr-3">
                                <input value="{{ app('request')->input('keyword') }}" name="keyword" width="100%" class="form-control" placeholder="Cari berdasarkan Kuesioner, Catin, Pasangan, Kode, Label, Petugas KB">
                            </div>
                            <button type="submit" class="btn btn-success">Search</button>
                        </form>

                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;overflow-x:auto !important;display: block;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kuesioner</th>
                                    <th>Catin</th>
                                    <th>Pasangan</th>
                                    <th>Waktu Pelaksanaan</th>
                                    <th>Kode</th>
                                    <th>Nilai Catin</th>
                                    <th>Label</th>
                                    <th>Petugas KB</th>
                                    <th>Ulasan Pendamping</th>
                                    <th width="4%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td>{{ $row['kuis_title'] }}</td>
                                    <td>{!! Helper::customUser($row['nama']) !!}<br /><small>{{ $row['kelurahan'] }}, {{ $row['kecamatan'] }}, {{ $row['kabupaten'] }}, {{ $row['provinsi'] }}</small></td>
                                    <td>{!! (isset($row['pasangan'])) ? Helper::customUser($row['pasangan']) : '-' !!}</td>
                                    <td>{{ $row['created_at'] }}</td>
                                    <td>{{ $row['kuis_code'] }}</td>
                                    <td>{{ $row['member_kuis_nilai'] }} / {{ $row['kuis_max_nilai'] }}</td>
                                    <td><button type="button" class="btn font-size-sm unclick" style="background-color: {{ $row['rating_color'] }}"><span class="font-weight-bolder text-white">{{ $row['label'] }}</span></button></td>
                                    <td>{{ (!empty($row['petugas_id'])) ? $row['petugas'] : '-' }}</td>
                                    <td>{{ (!empty($row['komentar'])) ? 'Sudah' : 'Belum' }}</td>
                                    <td style="white-space: nowrap;">
                                        <form method="POST" action="{{ route('admin.repkuis.details') }}">
                                            <input type="hidden" name="cu" value="{{ url()->full() }}">
                                            <input type="hidden" name="cid" value="{{ $row['id'] }}">
                                            <button type="submit" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Detail Hasil Kuis"><i class="flaticon2-writing"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginate-large" style="display: none;">
                            <div class="float-left">
                                @if (count($result) > 1)
                                    Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($result) }} dari {{ $paginate->total() }} data
                                @else
                                    Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} dari {{ $paginate->total() }} data
                                @endif
                            </div>
                            <div class="float-right">
                                {{ $paginate->appends($_GET)->links() }}
                            </div>
                        </div>
                        <div class="paginate-small" style="display: none;"> 
                            <div class="float-left">
                                @if (count($result) > 1)
                                    Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($result) }} dari {{ $paginate->total() }} data
                                @else
                                    Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} dari {{ $paginate->total() }} data
                                @endif
                            </div>
                            <div class="float-right">
                                @if($paginate->previousPageUrl() != null)
                                    <a href="{{$paginate->previousPageUrl()}}" class="btn btn-default pull-left"><i class="fa fa-chevron-left"></i> Sebelumnya</a>
                                @endif
                                &nbsp;
                                @if($paginate->nextPageUrl() != null)
                                    <a href="{{$paginate->nextPageUrl()}}" class="btn btn-default pull-right">Berikutnya <i class="fa fa-chevron-right"></i> </a>
                                @endif
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

<script type="text/javascript">
    $(document).ready(function() {
        if(screen.width < 768){
            $(".paginate-large").hide()
            $(".paginate-small").show()
        }else{
            $(".paginate-large").show()
            $(".paginate-small").hide()
        }
    //     var table = $('#kt_datatable').DataTable({
    //         "scrollX":"100%",
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
    });
</script>
@endpush

@endsection
