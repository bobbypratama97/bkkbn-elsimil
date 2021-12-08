@extends('layouts.master')
@push('css')
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Reporting - Hasil Kuesioner
                            <span class="d-block text-muted pt-2 font-size-sm">Halaman ini menampilkan data hasil kuisioner catin</span></h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Kuesioner :</label>
                                <select name="" class="form-control select2" id="kuesioner">
                                    <option value="">Pilih</option>
                                    @foreach ($kuis as $key => $row)
                                    <option value="{{ $row->id }}">{{ $row->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Rentang Waktu</label>
                                <div class='input-group' id='kt_daterangepicker'>
                                    <input type='text' class="form-control" readonly="readonly" placeholder="Select date range" name="tanggal" id="tanggal" />
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>Provinsi</label>
                                <select name="" class="form-control select2" id="provinsi">
                                    <option value="">Pilih</option>
                                    @foreach ($provinsi as $key => $row)
                                    <option value="{{ $row->provinsi_kode }}" {{ ($roles->role_id != '1') ? 'selected' : '' }}>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Kabupaten</label>
                                <select name="" class="form-control select2" id="kabupaten">
                                    <option value="">Pilih</option>
                                    @foreach ($kabupaten as $key => $row)
                                    <option value="{{ $row->kabupaten_kode }}" {{ ($roles->role_id != '1') ? 'selected' : '' }}>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Kecamatan</label>
                                <select name="" class="form-control select2" id="kecamatan">
                                    <option value="">Pilih</option>
                                    @foreach ($kecamatan as $key => $row)
                                    <option value="{{ $row->kecamatan_kode }}" {{ ($roles->role_id != '1') ? 'selected' : '' }}>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Kelurahan</label>
                                <select name="" class="form-control select2" id="kelurahan">
                                    <option value="">Pilih</option>
                                    @foreach ($kelurahan as $key => $row)
                                    <option value="{{ $row->kelurahan_kode }}">{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>NIK</label>
                                <input type="text" class="form-control" id="nik">
                            </div>
                            <div class="col-lg-3">
                                <label>Nama</label>
                                <input type="text" class="form-control" id="nama">
                            </div>
                            <div class="col-lg-3">
                                <label>Gender</label>
                                <select name="" class="form-control select2" id="gender">
                                    @foreach ($gender as $key => $row)
                                    <option value="{{ $key }}">{{ $row }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <button type="button" class="btn btn-success btn-block mt-8" id="lihat"><i class="flaticon-search"></i> Lihat</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom gutter-b" id="grafik-hasil">

                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Grafik - Hasil Kuesioner
                        </div>
                        <div class="card-toolbar">
                            <!-- <form method="POST" action="{{ route('admin.repkuis.download') }}" target="_blank" class="mr-3">
                                @csrf
                                <input type="hidden" name="kuesioner" id="detail-download-kuesioner">
                                <input type="hidden" name="tanggal" id="detail-download-tanggal">
                                <input type="hidden" name="provinsi" id="detail-download-provinsi">
                                <input type="hidden" name="kabupaten" id="detail-download-kabupaten">
                                <input type="hidden" name="kecamatan" id="detail-download-kecamatan">
                                <input type="hidden" name="kelurahan" id="detail-download-kelurahan">
                                <input type="hidden" name="nik" id="detail-download-nik">
                                <input type="hidden" name="nama" id ="detail-download-nama">
                                <input type="hidden" name="gender" id="detail-download-gender">
                                <button type="submit" class="btn btn-success"><i class="flaticon-notepad"></i> Download Report</button>
                            </form> -->
                            <form method="GET" action="{{ route('admin.repkuis.detail') }}" target="_blank">
                                @csrf
                                <input type="hidden" name="kuesioner" id="detail-kuesioner">
                                <input type="hidden" name="tanggal" id="detail-tanggal">
                                <input type="hidden" name="provinsi" id="detail-provinsi">
                                <input type="hidden" name="kabupaten" id="detail-kabupaten">
                                <input type="hidden" name="kecamatan" id="detail-kecamatan">
                                <input type="hidden" name="kelurahan" id="detail-kelurahan">
                                <input type="hidden" name="nik" id="detail-nik">
                                <input type="hidden" name="nama" id ="detail-nama">
                                <input type="hidden" name="gender" id="detail-gender">
                                <button type="submit" class="btn btn-primary"><i class="flaticon-notepad"></i> Detail Report</button>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            @for ($i = 1; $i < 20; $i++)
                            <div class="col-lg-4 mb-10" id="own-{{ $i }}">
                                <canvas class="charts" id="myChart-{{ $i }}"></canvas>
                            </div>
                            @endfor
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="{{ asset('assets/js/pages/crud/forms/widgets/bootstrap-daterangepicker.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#grafik-hasil').hide();
        $('.charts').hide();

        $('.select2').select2({
            placeholder: "Pilih",
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
            cancelClass:"btn-secondary"
        },(function(a,t,e){
            $("#kt_daterangepicker .form-control").val(a.format("DD/MM/YYYY")+" - "+t.format("DD/MM/YYYY"))
        }));

        $('#provinsi').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var provinsi = $('#provinsi').val();
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
                }
            });
        });

        $('#kabupaten').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kabupaten = $('#kabupaten').val();
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
                }
            });
        });

        $('#kecamatan').on('change', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kecamatan = $('#kecamatan').val();
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
                }
            });
        });

        $('#lihat').on('click', function() {
            $.preloader.start({
                modal:true,
                src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
            });

            var kuesioner = $('#kuesioner').val();
            var tanggal = $('#tanggal').val();
            var provinsi = $('#provinsi').val();
            var kabupaten = $('#kabupaten').val();
            var kecamatan = $('#kecamatan').val();
            var kelurahan = $('#kelurahan').val();
            var nama = $('#nama').val();
            var nik = $('#nik').val();
            var gender = $('#gender').val();

            if (kuesioner == '') {
                bootbox.alert({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'>Kuesioner harus dipilih</p>",
                    size: 'small'
                });
            }
            else if(tanggal == '') {
                bootbox.alert({
                    title: 'Perhatian',
                    centerVertical: true,
                    closeButton: false,
                    message: "<p class='text-center'>Range Tanggal harus dipilih</p>",
                    size: 'small'
                });
            }
            else {

                $('#detail-kuesioner').val(kuesioner);
                $('#detail-tanggal').val(tanggal);
                $('#detail-provinsi').val(provinsi);
                $('#detail-kabupaten').val(kabupaten);
                $('#detail-kecamatan').val(kecamatan);
                $('#detail-kelurahan').val(kelurahan);
                $('#detail-nama').val(nama);
                $('#detail-nik').val(nik);
                $('#detail-gender').val(gender);

                $('#detail-download-kuesioner').val(kuesioner);
                $('#detail-download-tanggal').val(tanggal);
                $('#detail-download-provinsi').val(provinsi);
                $('#detail-download-kabupaten').val(kabupaten);
                $('#detail-download-kecamatan').val(kecamatan);
                $('#detail-download-kelurahan').val(kelurahan);
                $('#detail-download-nama').val(nama);
                $('#detail-download-nik').val(nik);
                $('#detail-download-gender').val(gender);

                /*$.preloader.start({
                    modal:true,
                    src : baseurl + '/assets/plugins/spinner/img/sprites.24.png'
                });*/

                $('#grafik-hasil').hide();

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.repkuis.search') }}',
                    data: { 
                        "_token": "{{ csrf_token() }}", 
                        "kuesioner": kuesioner, 
                        "tanggal": tanggal, 
                        "provinsi": provinsi, 
                        "kabupaten": kabupaten, 
                        "kecamatan": kecamatan, 
                        "kelurahan": kelurahan, 
                        "nama": nama, 
                        "nik": nik, 
                        "gender": gender 
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.count == '0') {
                            bootbox.alert({
                                title: 'Perhatian',
                                centerVertical: true,
                                closeButton: false,
                                message: "<p class='text-center'>Data belum tersedia untuk pencarian ini",
                                size: 'small'
                            });
                        } else {
                            $('#grafik-hasil').show();

                            for (var i = 1; i < 20; i++) {
                                $('#own-' + i).hide();
                                $('#myChart-' + i).hide();
                            }

                            $.each(data.data, function(index, item) {

                                $('#myChart-' + index).show();
                                $('#own-' + index).show();

                                var ctx = document.getElementById('myChart-' + index).getContext('2d');
                                var myChart = new Chart(ctx, {
                                    animation: 'easeInQuad',
                                    type: 'pie',
                                    data: {
                                        labels: item.legend,
                                        datasets: [{
                                            label: item.label,
                                            data: item.value,
                                            backgroundColor: item.color
                                        }],
                                    },
                                    options: {
                                        showAllTooltips: true,
                                        responsive: true,
                                        title: {
                                            display: true,
                                            text: item.label
                                        },
                                        tooltips: {
                                            mode: 'dataset',
                                            intersect: false
                                        },
                                        legend: {
                                            position: 'right'
                                        },
                                    }
                                });
                            });
                        }

                        //$.preloader.stop();

                    },
                    failure: function(errMsg) {
                        alert(errMsg);
                    }
                });
            }

            $.preloader.stop();
        });
    });
</script>
@endpush

@endsection
