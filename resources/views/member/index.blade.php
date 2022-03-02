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
                        <form class="mb-7" method="GET" action="{{ route('admin.member.index') }}">
                            <!-- <div class="form-group mr-3">
                                <label for="email">Cari : </label>
                                <select name="s" class="form-control ml-3">
                                    <option value="">Pilih</option>
                                    <option value="all" {{ (isset($search) && $search == "all") ? "selected" : ""}}>Semua</option>
                                    <option value="m" {{ (isset($search) && $search == "m") ? "selected" : ""}}>Catin saya</option>
                                    <option value="h" {{ (isset($search) && $search == "h") ? "selected" : ""}}>Sudah punya petugas</option>
                                    <option value="nh" {{ (isset($search) && $search == "nh") ? "selected" : ""}}>Belum punya petugas</option>
                                    <option value="hp" {{ (isset($search) && $search == "hp") ? "selected" : ""}}>Berdasarkan Nomor HP</option>
                                </select>
                            </div> -->

                            <!-- <div class="form-group mr-3">
                                <label for="name">Keyword : </label>
                                <input type="search" name="name" value="{{ (isset($name)) ? $name : ""}}"  class="form-control form-control-sm ml-3" placeholder="" aria-controls="kt_datatable" _vkenabled="true">
                            </div>
                            <button type="submit" class="btn btn-success">Filter </button> -->

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label>Keyword</label>
                                    <input value="{{ app('request')->input('keyword') }}" name="keyword" width="100%" class="form-control" placeholder="Cari berdasarkan Nama">
                                </div>
                                <div class="col-lg-3">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" id="gender" data-allow-clear="{{$roles->role_id <5?"true":""}}">
                                        <option value="">Pilih</option>
                                        <option value="1" {{ (isset($gender) && $gender == "1") ? 'selected' : '' }}>Pria</option>
                                        <option value="2" {{ (isset($gender) && $gender == "2") ? 'selected' : '' }}>Wanita</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control" id="status" data-allow-clear="{{$roles->role_id <5?"true":""}}">
                                        <option value="">Pilih</option>
                                        <option value="1" {{ (isset($status) && $status == "1") ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ (isset($status) && $status == "0") ? 'selected' : '' }}>Tidak Aktif</option>
                                        <!-- <option value="2" {{ (isset($status) && $status == "2") ? 'selected' : '' }}>Banned</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label>Provinsi </label>
                                    <select name="provinsi" class="form-control select2" id="provinsi" data-allow-clear="{{$roles->role_id <2?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($provinsi as $key => $row)
                                        <option value="{{ $row->provinsi_kode }}" {{ ($roles->role_id != '1') ? 'selected' : (($selected_region['prov'] == $row->provinsi_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Kabupaten</label>
                                    <select name="kabupaten" class="form-control select2" id="kabupaten" data-allow-clear="{{$roles->role_id <3?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($kabupaten as $key => $row)
                                        <option value="{{ $row->kabupaten_kode }}" {{ ($roles->role_id != '1' && $roles->role_id != '2') ? 'selected' : (($selected_region['kab'] == $row->kabupaten_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Kecamatan</label>
                                    <select name="kecamatan" class="form-control select2" id="kecamatan" data-allow-clear="{{$roles->role_id <4?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($kecamatan as $key => $row)
                                        <option value="{{ $row->kecamatan_kode }}" {{ ($roles->role_id != '1' && $roles->role_id != '2' && $roles->role_id != '3') ? 'selected' : (($selected_region['kec'] == $row->kecamatan_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Kelurahan</label>
                                    <select name="kelurahan" class="form-control select2" id="kelurahan" data-allow-clear="{{$roles->role_id <5?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($kelurahan as $key => $row)
                                        <option value="{{ $row->kelurahan_kode }}" {{ ($roles->role_id != '1' && $roles->role_id != '2' && $roles->role_id != '3' && $roles->role_id != '4') ? 'selected' : (($selected_region['kel'] == $row->kelurahan_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label>Petugas Pendamping</label>
                                    <input value="{{ app('request')->input('petugas') }}" name="petugas" width="100%" class="form-control" placeholder="Cari berdasarkan Petugas">
                                </div>

                                <div class="col-lg-3">
                                    <label>Status Pendamping</label>
                                    <select name="status_pendamping" class="form-control" id="status_pendamping" data-allow-clear="{{$roles->role_id <5?"true":""}}">
                                        <option value="">Pilih</option>
                                        <option value="1" {{ (isset($status_pendamping) && $status_pendamping == "1") ? 'selected' : '' }}>Ada</option>
                                        <option value="0" {{ (isset($status_pendamping) && $status_pendamping == "0") ? 'selected' : '' }}>Tidak Ada</option>
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-success btn-block">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;overflow-x:auto !important;display:block;white-space: normal;">
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
                                    <th>Petugas Pendamping</th>
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
                                    <td>
                                        @if(!empty($row['petugas']))
                                            @php
                                                $petugas_arr = explode(',', $row['petugas'])
                                            @endphp
                                            @foreach ($petugas_arr as $petugas)
                                                <a href="{{route('admin.user.index', ['name' => $petugas])}}">{{$petugas}}</a>,
                                            @endforeach
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-right" width="14%" style="white-space: nowrap">
                                        {{-- @if ($row['gender'] == 2)
                                            <a href="{{ route('admin.member.ibuhamil', $row['id']) }}" class="btn btn-icon btn-sm btn-primary"  title="Tambah Kuesioner Ibu Hamil" style="background-color: #EB30EF">
                                                <i class="flaticon2-notepad"></i>
                                            </a>
                                        @endif --}}
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
                                            <i class="flaticon2-edit"></i>
                                        </a>
                                        @endcan
                                        @if (empty($row['petugas_id']) && $is_dampingi == true)
                                        <button class="btn btn-icon btn-sm btn-warning kelola" id="kelola"  title="Dampingi catin" data-id="{{ $row['id'] }}">
                                            <i class="flaticon-businesswoman"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm btn-default btndisable" id="kelola"  title="Chat catin" data-id="{{ $row['id'] }}">
                                                <i class="flaticon-chat"></i>
                                        </button>
                                        @elseif ($row['petugas_id'] == Auth::user()->id)
                                        <button class="btn btn-icon btn-sm btn-default btndisableself" id="kelola"  title="Dampingi catin" data-id="{{ $row['id'] }}">
                                                <i class="flaticon-businesswoman"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm btn-primary chatcatin" id="kelola"  title="Chat catin" data-id="{{ $row['id'] }}">
                                                <i class="flaticon-chat"></i>
                                        </button>
                                        @else
                                        <button class="btn btn-icon btn-sm btn-default btndisable" id="kelola"  title="Dampingi catin" data-id="{{ $row['id'] }}">
                                                <i class="flaticon-businesswoman"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm btn-default btndisable" id="kelola"  title="Chat catin" data-id="{{ $row['id'] }}">
                                                <i class="flaticon-chat"></i>
                                        </button>
                                        @endif

                                        @can('access', [\App\Member::class, Auth::user()->role, 'delete'])
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
                    </div>
                </div>
                <div class="paginate-large" style="display: none;">
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
                <div class="paginate-small" style="display: none;"> 
                    <div class="float-left">
                        @if (count($member) > 1)
                            Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($member) }} dari {{ $paginate->total() }} data
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
                            <a href="{{$paginate->nextPageUrl()}}" class="btn btn-default pull-right">Berikutnya <i class="fa fa-chevron-right"></i></a>
                        @endif
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
        $('.select2').select2({
            placeholder: "Pilih",
			    allowClear: true,

            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#provinsi').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var provinsi = $('#provinsi').val();
            if(provinsi == '' || provinsi == null) {
                $('#kabupaten, #kecamatan, #kelurahan').empty().trigger('change');
                $.preloader.stop();
                return true
            }
            
            $.ajax({
                type: "POST",
                url: '{{ route('kabupaten') }}',
                data: { "_token": "{{ csrf_token() }}", "provinsi_id": provinsi },
                dataType: "json",
                success: function(data) {
                    $('#kabupaten').html(data.content);
                    $('#kecamatan').html('');
                    $('#kelurahan').html('');

                    $.preloader.stop();
                },
                failure: function(errMsg) {
                    alert(errMsg);
                    $.preloader.stop();
                }
            });
        });

        $('#kabupaten').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kabupaten = $('#kabupaten').val();
            if(kabupaten == '' || kabupaten == null) {
                // $('#kecamatan').select2('destroy');
                // $('#kecamatan').empty();
                // $('#kecamatan').select2({'placeholder': 'Pilih'});
                $('#kecamatan, #kelurahan').empty().trigger('change');

                $.preloader.stop();
                return true
            }

            $.ajax({
                type: "POST",
                url: '{{ route('kecamatan') }}',
                data: { "_token": "{{ csrf_token() }}", "kabupaten_id": kabupaten },
                dataType: "json",
                success: function(data) {
                    $('#kecamatan').html(data.content);
                    $('#kelurahan').html('');

                    $.preloader.stop();
                },
                failure: function(errMsg) {
                    alert(errMsg);
                    $.preloader.stop();
                }
            });
        });

        $('#kecamatan').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kecamatan = $('#kecamatan').val();
            
            if(kecamatan == '' || kecamatan == null) {
                $('#kelurahan').empty().trigger('change');
                $.preloader.stop();
                return true
            }

            $.ajax({
                type: "POST",
                url: '{{ route('kelurahan') }}',
                data: { "_token": "{{ csrf_token() }}", "kecamatan_id": kecamatan },
                dataType: "json",
                success: function(data) {
                    $('#kelurahan').html(data.content);

                    $.preloader.stop();
                },
                failure: function(errMsg) {
                    alert(errMsg);
                    $.preloader.stop();
                }
            });
        });

        if(screen.width < 768){
            $(".paginate-large").hide()
            $(".paginate-small").show()
        }else{
            $(".paginate-large").show()
            $(".paginate-small").hide()
        }
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
                        url: '{{ route('admin.member.delete') }}',
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
                                            window.location.href = '{{ route('admin.member.index') }}';
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
                                                // window.location.href = '{{ route("admin.member.show", '+id+') }}';
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
                                                let url = '{{ route("admin.member.show", ":queryId") }}';
                                                url = url.replace(':queryId', id);
                                                // window.location.href = '{{ route('admin.member.index') }}';
                                                window.location.href = url;
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

    $('#kt_datatable tbody').on('click', '.btndisable', function () {
        bootbox.dialog({
            title: 'Perhatian',
            centerVertical: true,
            closeButton: false,
            message: "Untuk mendampingin catin ini harap menguhubungi petugas KB di Tim anda.",
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

    $('#kt_datatable tbody').on('click', '.btndisableself', function () {
        bootbox.dialog({
            title: 'Perhatian',
            centerVertical: true,
            closeButton: false,
            message: "Catin telah menjadi tanggung jawab anda.",
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

    $('#kt_datatable tbody').on('click', '.chatcatin', function () {
        var id = $(this).attr('data-id');

        bootbox.confirm({
            title: 'Perhatian',
            message: "<p class='text-center'>Mulai percakapan ?</p>",
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
                        url: '{{ route('admin.chat.create') }}',
                        type: 'POST',
                        data: {member_id : id, '_token': "{{ csrf_token() }}"},
                        dataType: 'json',
                        success: function( data ) {
                            $.preloader.stop();

                            if (data.redirect == 0) {
                                bootbox.dialog({
                                    title: 'Perhatian',
                                    centerVertical: true,
                                    closeButton: false,
                                    message: "<p class='text-center'>" + data.msg + "</p>",
                                    buttons: {
                                        ok: {
                                            label: "OK",
                                            className: 'btn-info',
                                            callback: function() {
                                                // if(data.redirect == 1){
                                                //     window.location.href = data.url;
                                                // }
                                            }
                                        }
                                    }
                                });
                            } else {
                                if(data.redirect == 1){
                                    window.location.href = data.url;
                                }
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
