@extends('layouts.master')
@push('css')
<style>
    a.unclick { pointer-events: none; cursor: default; }
</style>
@endpush

@section('content')

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">

                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            <form method="POST" action="{{ route('admin.member.blokir') }}" class="form mr-3 pt-3">
                                @csrf
                                <input type="hidden" name="cid" value="{{ $member->id }}">
                                <button type="submit" class="btn btn-success font-weight-bold py-3 px-6 mb-2 text-center btn-block">Update Data Catin</button>
                            </form>

                            <a href="{{ route('admin.member.index') }}" class="btn btn-danger">Kembali</a>
                        </div>
                    </div>
                </div>

                <div class="card card-custom gutter-b example example-compact">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Data Admin CMS :</h3>
                    </div>

                    <form method="POST" action="{{ route('admin.member.update', $member->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" class="form-control" value="{!! Helper::decryptNik($member->no_ktp) !!}" disabled />
                            </div>

                            <div class="form-group">
                                <label>No Telepon</label>
                                <input type="text" class="form-control" value="{{ $member->no_telp }}" disabled />
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{ $member->email }}" disabled />
                            </div>

                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" value="{{ $member->name }}" required />
                            </div>

                            <div class="form-group row">
                                <div class="col-7">
                                    <div class="form-group">
                                        <label>Alamat Sesuai Domisili</label>
                                        <input class="form-control" value="{{ $member->alamat }}" />
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label>RT</label>
                                        <input class="form-control" value="{{ $member->rt }}" />
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label>RW</label>
                                        <input class="form-control" value="{{ $member->rw }}" />
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kode POS</label>
                                        <input class="form-control" value="{{ $member->kodepos }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Provinsi</label>
                                        <select class="form-control select2" id="provinsi" name="provinsi_id" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kabupaten</label>
                                        <select class="form-control select2" id="kabupaten" name="kabupaten_id" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <input type="text" class="form-control" value="" disabled />
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Kelurahan</label>
                                        <input type="text" class="form-control" value="" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <select class="form-control" id="jk" name="jk" required>
                                            <option {{$member->gender == 1 ? "selected" : null}}>Laki - Laki</option>
                                            <option {{$member->gender == 2 ? "selected" : null}}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>Tempat</label>
                                        <input type="text" class="form-control" value="{{$member->tempat_lahir}}" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="text" class="form-control" value="{{$member->tgl_lahir}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('script')
<script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/plugins/spinner/jquery.preloaders.js') }}"></script>
<script src="{{ asset('assets/plugins/bootbox/bootbox.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#provinsi').select2({
            placeholder: "Pilih Provinsi",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#kabupaten').select2({
            placeholder: "Pilih Kabupaten",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#kecamatan').select2({
            placeholder: "Pilih Kecamatan",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $('#kelurahan').select2({
            placeholder: "Pilih Kelurahan",
            "language": {
                "noResults": function(){
                    return "Tidak ada data";
                }
            },    
        });

        $.ajax({
            type: "POST",
            url: '{{ route('provinsi') }}',
            data: { "_token": "{{ csrf_token() }}" },
            dataType: "json",
            success: function(data) {
                $('#provinsi').html(data.content);
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });
    });

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
            },
            failure: function(errMsg) {
                alert(errMsg);
            }
        });

        $.preloader.stop();
    });
</script>
@endpush

@endsection
