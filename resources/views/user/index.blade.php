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
                @if ($errors->has('error'))
                <div class="alert alert-custom alert-danger" role="alert">
                    <div class="alert-icon">
                        <i class="flaticon-warning"></i>
                    </div>
                    <div class="alert-text"><strong>{{ $errors->first('error') }}</strong><br />{{ $errors->first('keterangan') }}</div>
                </div>
                @endif

                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Admin CMS 
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data admin CMS</span></h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="mb-7" method="GET" action="{{ route('admin.user.index') }}">
                            <!-- <div class="form-group mr-3">
                                <label for="name">Nama Admin : </label>
                                <input type="search" name="name" value="{{ (isset($name)) ? $name : ""}}"  class="form-control form-control-sm ml-3" placeholder="" aria-controls="kt_datatable" _vkenabled="true">
                            </div>
                            <button type="submit" class="btn btn-success">Filter </button> -->

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label>Keyword</label>
                                    <input value="{{ $keyword }}" id="keyword" name="keyword" width="100%" class="form-control" placeholder="Cari berdasarkan Nama">
                                </div>
                                <div class="col-lg-3">
                                    <label>Status</label>
                                    <select name="status" class="form-control" id="status" data-allow-clear="{{$role <5?"true":""}}">
                                        @foreach($status_list as $key => $val)
                                            <option value="{{$key}}" {{ (isset($status) && $status == $key) ? 'selected' : '' }}>{{$val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label>Provinsi </label>
                                    <select name="provinsi" class="form-control select2" id="provinsi" data-allow-clear="{{$role <2?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($provinsi as $key => $row)
                                        <option value="{{ $row->provinsi_kode }}" {{ ($role != '1') ? 'selected' : (($selected_region['prov'] == $row->provinsi_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Kabupaten</label>
                                    <select name="kabupaten" class="form-control select2" id="kabupaten" data-allow-clear="{{$role <3?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($kabupaten as $key => $row)
                                        <option value="{{ $row->kabupaten_kode }}" {{ ($role != '1' && $role != '2') ? 'selected' : (($selected_region['kab'] == $row->kabupaten_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Kecamatan</label>
                                    <select name="kecamatan" class="form-control select2" id="kecamatan" data-allow-clear="{{$role <4?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($kecamatan as $key => $row)
                                        <option value="{{ $row->kecamatan_kode }}" {{ ($role != '1' && $role != '2' && $role != '3') ? 'selected' : (($selected_region['kec'] == $row->kecamatan_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Kelurahan</label>
                                    <select name="kelurahan" class="form-control select2" id="kelurahan" data-allow-clear="{{$role <5?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($kelurahan as $key => $row)
                                        <option value="{{ $row->kelurahan_kode }}" {{ ($role != '1' && $role != '2' && $role != '3' && $role != '4') ? 'selected' : (($selected_region['kel'] == $row->kelurahan_kode) ? 'selected' : '') }}>{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-3">
                                    <label>Jumlah Member</label>
                                    <input type="number" value="{{ $member_sum }}" id="member_sum" name="member_sum" width="100%" class="form-control" placeholder="Cari berdasarkan jumlah member">
                                </div>

                                <div class="col-lg-3">
                                    <label>Role</label>
                                    <select name="role_id" class="form-control" id="role" data-allow-clear="{{$role <5?"true":""}}">
                                        <option value="">Pilih</option>
                                        @foreach ($role_list as $value)
                                            <option value="{{$value['id']}}" {{ (isset($role_id) && $role_id == $value['id']) ? 'selected' : '' }}>{{$value['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3">
                                    <label>Rentang Waktu</label>
                                    <div class='input-group' id='kt_daterangepicker'>
                                        <input type='text' class="form-control" readonly="readonly" placeholder="Select date range" name="tanggal" id="tanggal" value="{{$tanggal}}"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-1">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button onclick="clearFilter()" type="button" class="btn btn-warning btn-block">Clear</button>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-success btn-block">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-bordered table-checkable" id="kt_datatable" style="border-collapse: collapse; border-spacing: 0; width: 100% !important;overflow-x:auto !important;display: block;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th width="25%">Lokasi</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Jumlah Member</th>
                                    <th width="14%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($user))
                                    <tr>
                                        <td colspan="10" align="center">Data tidak ditemukan</td>
                                    </tr>
                                @endif
                                @foreach($user as $key => $row)
                                <tr>
                                    <td>{{ ($paginate->currentPage() * 10) - 10 + $key + 1 }}</td>
                                    <td>{!! Helper::decryptNik($row->nik) !!}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td>{{ $row->kelurahan }}, {{ $row->kecamatan }}, {{ $row->kabupaten }}, {{ $row->provinsi }}</td>
                                    <td>{!! Helper::statusAdmin($row->is_active) !!}</td>
                                    <td>{{ $row->roles }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{{ $row->total }}</td>
                                    <td class="text-center" width="14%" style=" white-space: nowrap;">
                                        @if($role < $row->role_id || $row->id == Auth::user()->id)
                                        <a href="{{ route('admin.user.delegasi', $row->id) }}" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="Pendampingan Catin">
                                            <i class="flaticon2-avatar"></i>
                                        </a>
                                        @else
                                        <a href="#" class="btn btn-icon btn-sm btn-default btndisable" data-toggle="tooltip" data-placement="top" title="Pendampingan Catin">
                                            <i class="flaticon2-avatar"></i>
                                        </a>
                                        @endif
                                        
                                        @can('access', [\App\User::class, $role, 'show'])
                                        <a href="{{ route('admin.user.show', $row->id) }}" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Detail">
                                            <i class="flaticon2-menu-1"></i>
                                        </a>
                                        @endcan

                                        @can('access', [\App\User::class, Auth::user()->role, 'edit'])
                                        @if($role < $row->role_id || $row->id == Auth::user()->id)
                                        <a href="{{ route('admin.user.edit', $row->id) }}" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Ubah">
                                            <i class="flaticon2-edit"></i>
                                        </a>
                                        @else
                                        <a href="#" class="btn btn-icon btn-sm btn-default btndisable" data-toggle="tooltip" data-placement="top" title="Ubah">
                                            <i class="flaticon2-edit"></i>
                                        </a>
                                        @endif
                                        @endcan
                                        @can('access', [\App\User::class, Auth::user()->role, 'delete'])
                                        @if($role < $row->role_id || $row->id == Auth::user()->id)
                                        <button class="btn btn-icon btn-sm btn-danger hapus" id="hapus"  data-toggle="tooltip"
                                                data-placement="top" title="Hapus" data-id="{{ $row->id }}">
                                            <i class="flaticon2-trash"></i>
                                        </button>
                                        @else
                                        <button class="btn btn-icon btn-sm btn-default btndisable" id="hapus"  data-toggle="tooltip"
                                                data-placement="top" title="Hapus" data-id="{{ $row->id }}">
                                            <i class="flaticon2-trash"></i>
                                        </button>
                                        @endif
                                       @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginate-large" style="display: none;">
                            <div class="float-left">
                                @if (count($user) > 1)
                                    Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($user) }} dari {{ $paginate->total() }} data
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
                                @if (count($user) > 1)
                                    Menampilkan {{ ($paginate->currentPage() * 10) - 10 + 1 }} sampai {{ (($paginate->currentPage() * 10) - 10) + count($user) }} dari {{ $paginate->total() }} data
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
        var daterange = $('#tanggal').val()
        var startdate = enddate = new Date()

        if(daterange != '') {
            var daterangearr = daterange.split('-')
            
            startdate = daterangearr[0].split('/')
            startdate = new Date(startdate[2], startdate[1] - 1, startdate[0])
            enddate = daterangearr[1].split('/')
            enddate = new Date(enddate[2], enddate[1] - 1, enddate[0])
        }

        $('.select2').select2({
            placeholder: "Pilih",
			    allowClear: true,

            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $("#kt_daterangepicker").daterangepicker({
            dateLimit: {
                'months': 3,
                'days': -1
            },
            showDropdowns: false,
            buttonClasses:" btn",
            applyClass:"btn-primary",
            cancelClass:"btn-secondary",
            startDate: startdate,
            endDate: enddate,
        },(function(a,t,e){
            $("#kt_daterangepicker .form-control").val(a.format("DD/MM/YYYY")+"-"+t.format("DD/MM/YYYY"))
        }));

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
                        url: '{{ route('admin.user.delete') }}',
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
                                            window.location.href = '{{ route('admin.user.index') }}';
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

    $('#kt_datatable tbody').on('click', '.btndisable', function () {
        bootbox.dialog({
            title: 'Perhatian',
            centerVertical: true,
            closeButton: false,
            message: "Anda tidak dapat mengakses halaman ini.",
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

    function clearFilter() {
        $('#keyword').val("")
        $('#member_sum').val("")
        $('#tanggal').val("").datepicker("update")
    }
</script>
@endpush

@endsection
